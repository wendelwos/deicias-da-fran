<?php
/**
 * Product Admin Controller - CRUD for Products and Categories
 */

class ProductAdminController
{

    public function __construct()
    {
        if (!auth()) {
            redirect('login');
            return;
        }

        // Only admin can access
        if (!hasRole('admin')) {
            redirect('cozinha');
            return;
        }
    }

    // ==========================================
    // PRODUCTS
    // ==========================================

    public function index(): void
    {
        $category = $_GET['category'] ?? null;

        $where = "1=1";
        $params = [];

        if ($category) {
            $where .= " AND p.category_id = ?";
            $params[] = $category;
        }

        $products = Database::fetchAll("
            SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON c.id = p.category_id 
            WHERE $where 
            ORDER BY c.display_order, p.display_order
        ", $params);

        $categories = Database::fetchAll("
            SELECT * FROM categories ORDER BY display_order
        ");

        view('admin/products/index', [
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $category,
        ]);
    }

    public function create(): void
    {
        $categories = Database::fetchAll("
            SELECT * FROM categories ORDER BY display_order
        ");

        view('admin/products/form', [
            'product' => null,
            'categories' => $categories,
            'options' => [],
        ]);
    }

    public function store(): void
    {
        $data = $this->validateProduct($_POST);

        if (isset($data['errors'])) {
            $_SESSION['errors'] = $data['errors'];
            $_SESSION['old_input'] = $_POST;
            redirect('admin/produtos/novo');
            return;
        }

        $productId = Database::insert('products', $data);

        // Handle options for buildable products
        if (!empty($_POST['options'])) {
            $this->saveProductOptions($productId, $_POST['options']);
        }

        flash('success', 'Produto criado com sucesso!');
        redirect('admin/produtos');
    }

    public function edit(string $id): void
    {
        $product = Database::fetch("SELECT * FROM products WHERE id = ?", [$id]);

        if (!$product) {
            redirect('admin/produtos');
            return;
        }

        $categories = Database::fetchAll("
            SELECT * FROM categories ORDER BY display_order
        ");

        $options = Database::fetchAll("
            SELECT * FROM product_options 
            WHERE product_id = ? 
            ORDER BY option_type, display_order
        ", [$id]);

        view('admin/products/form', [
            'product' => $product,
            'categories' => $categories,
            'options' => $options,
        ]);
    }

    public function update(string $id): void
    {
        $data = $this->validateProduct($_POST);

        if (isset($data['errors'])) {
            $_SESSION['errors'] = $data['errors'];
            $_SESSION['old_input'] = $_POST;
            redirect("admin/produtos/$id/editar");
            return;
        }

        // Handle image upload
        if (!empty($_FILES['product_image']['tmp_name'])) {
            $imageName = $this->saveUploadedImage($_FILES['product_image'], $id);
            if ($imageName) {
                $data['image'] = $imageName;
            }
        }

        Database::update('products', $data, 'id = ?', [$id]);

        // Handle options
        if (isset($_POST['options'])) {
            // Delete old options
            Database::delete('product_options', 'product_id = ?', [$id]);
            // Save new options
            $this->saveProductOptions($id, $_POST['options']);
        }

        flash('success', 'Produto atualizado com sucesso!');
        redirect('admin/produtos');
    }

    /**
     * Save uploaded image file
     */
    private function saveUploadedImage(array $file, string $productId): ?string
    {
        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        // Check file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            return null;
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . $productId . '_' . time() . '.' . $ext;
        $filepath = ROOT_PATH . '/assets/img/' . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }

        return null;
    }

    public function destroy(string $id): void
    {
        // Delete options first
        Database::delete('product_options', 'product_id = ?', [$id]);
        // Delete product
        Database::delete('products', 'id = ?', [$id]);

        flash('success', 'Produto removido com sucesso!');
        redirect('admin/produtos');
    }

    public function toggleActive(string $id): void
    {
        $product = Database::fetch("SELECT active FROM products WHERE id = ?", [$id]);

        if ($product) {
            $newStatus = $product['active'] ? 0 : 1;
            Database::update('products', ['active' => $newStatus], 'id = ?', [$id]);
            json(['success' => true, 'active' => $newStatus]);
        } else {
            json(['success' => false, 'message' => 'Produto não encontrado']);
        }
    }

    private function validateProduct(array $input): array
    {
        $errors = [];

        $name = trim($input['name'] ?? '');
        $categoryId = $input['category_id'] ?? null;
        $price = floatval($input['price'] ?? 0);

        if (empty($name)) {
            $errors['name'] = 'Nome é obrigatório';
        }

        if (!$categoryId) {
            $errors['category_id'] = 'Categoria é obrigatória';
        }

        if ($price <= 0) {
            $errors['price'] = 'Preço deve ser maior que zero';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'category_id' => $categoryId,
            'name' => $name,
            'description' => trim($input['description'] ?? ''),
            'price' => $price,
            'price_combo' => !empty($input['price_combo']) ? floatval($input['price_combo']) : null,
            'is_combo' => isset($input['is_combo']) ? 1 : 0,
            'is_buildable' => isset($input['is_buildable']) ? 1 : 0,
            'max_ingredients' => intval($input['max_ingredients'] ?? 0),
            'max_seasonings' => intval($input['max_seasonings'] ?? 0),
            'display_order' => intval($input['display_order'] ?? 0),
            'active' => isset($input['active']) ? 1 : 1, // Default to active
        ];
    }

    private function saveProductOptions(int $productId, array $options): void
    {
        foreach ($options as $type => $items) {
            if (!is_array($items))
                continue;

            foreach ($items as $i => $item) {
                if (empty(trim($item)))
                    continue;

                Database::insert('product_options', [
                    'product_id' => $productId,
                    'option_type' => $type,
                    'name' => trim($item),
                    'display_order' => $i + 1,
                ]);
            }
        }
    }

    // ==========================================
    // CATEGORIES
    // ==========================================

    public function categories(): void
    {
        $categories = Database::fetchAll("
            SELECT c.*, 
                   (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
            FROM categories c 
            ORDER BY c.display_order
        ");

        view('admin/categories/index', [
            'categories' => $categories,
        ]);
    }

    public function createCategory(): void
    {
        view('admin/categories/form', [
            'category' => null,
        ]);
    }

    public function storeCategory(): void
    {
        $name = trim($_POST['name'] ?? '');
        $slug = $this->generateSlug($name);

        if (empty($name)) {
            $_SESSION['errors'] = ['name' => 'Nome é obrigatório'];
            redirect('admin/categorias/nova');
            return;
        }

        Database::insert('categories', [
            'name' => $name,
            'slug' => $slug,
            'description' => trim($_POST['description'] ?? ''),
            'display_order' => intval($_POST['display_order'] ?? 0),
            'active' => 1,
        ]);

        flash('success', 'Categoria criada com sucesso!');
        redirect('admin/categorias');
    }

    public function editCategory(string $id): void
    {
        $category = Database::fetch("SELECT * FROM categories WHERE id = ?", [$id]);

        if (!$category) {
            redirect('admin/categorias');
            return;
        }

        view('admin/categories/form', [
            'category' => $category,
        ]);
    }

    public function updateCategory(string $id): void
    {
        $name = trim($_POST['name'] ?? '');

        if (empty($name)) {
            $_SESSION['errors'] = ['name' => 'Nome é obrigatório'];
            redirect("admin/categorias/$id/editar");
            return;
        }

        Database::update('categories', [
            'name' => $name,
            'slug' => $this->generateSlug($name),
            'description' => trim($_POST['description'] ?? ''),
            'display_order' => intval($_POST['display_order'] ?? 0),
        ], 'id = ?', [$id]);

        flash('success', 'Categoria atualizada com sucesso!');
        redirect('admin/categorias');
    }

    public function destroyCategory(string $id): void
    {
        // Check if has products
        $productCount = Database::fetch("SELECT COUNT(*) as count FROM products WHERE category_id = ?", [$id])['count'];

        if ($productCount > 0) {
            flash('error', 'Não é possível remover categoria com produtos. Remova os produtos primeiro.');
            redirect('admin/categorias');
            return;
        }

        Database::delete('categories', 'id = ?', [$id]);

        flash('success', 'Categoria removida com sucesso!');
        redirect('admin/categorias');
    }

    private function generateSlug(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[áàãâä]/u', 'a', $text);
        $text = preg_replace('/[éèêë]/u', 'e', $text);
        $text = preg_replace('/[íìîï]/u', 'i', $text);
        $text = preg_replace('/[óòõôö]/u', 'o', $text);
        $text = preg_replace('/[úùûü]/u', 'u', $text);
        $text = preg_replace('/[ç]/u', 'c', $text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        return $text;
    }
}
