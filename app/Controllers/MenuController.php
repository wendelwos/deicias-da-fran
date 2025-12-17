<?php
/**
 * Menu Controller
 */

class MenuController
{

    public function index(): void
    {
        $categories = Database::fetchAll("
            SELECT * FROM categories 
            WHERE active = 1 
            ORDER BY display_order
        ");

        $products = Database::fetchAll("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM products p 
            JOIN categories c ON c.id = p.category_id 
            ORDER BY c.display_order, p.display_order
        ");

        // Group products by category
        $grouped = [];
        foreach ($products as $product) {
            $catSlug = $product['category_slug'];
            if (!isset($grouped[$catSlug])) {
                $grouped[$catSlug] = [];
            }
            $grouped[$catSlug][] = $product;
        }

        // Get buildable product options
        $buildableProducts = Database::fetchAll("
            SELECT id FROM products WHERE is_buildable = 1 AND active = 1
        ");

        $productOptions = [];
        foreach ($buildableProducts as $bp) {
            $productOptions[$bp['id']] = Database::fetchAll("
                SELECT * FROM product_options 
                WHERE product_id = ? AND active = 1 
                ORDER BY option_type, display_order
            ", [$bp['id']]);
        }

        view('public/menu', [
            'categories' => $categories,
            'products' => $grouped,
            'productOptions' => $productOptions,
        ]);
    }

    public function category(string $slug): void
    {
        $category = Database::fetch("
            SELECT * FROM categories WHERE slug = ? AND active = 1
        ", [$slug]);

        if (!$category) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $products = Database::fetchAll("
            SELECT * FROM products 
            WHERE category_id = ? AND active = 1 
            ORDER BY display_order
        ", [$category['id']]);

        view('public/category', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
