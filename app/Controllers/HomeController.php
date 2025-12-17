<?php
/**
 * Home Controller
 */

class HomeController
{

    public function index(): void
    {
        $categories = Database::fetchAll("
            SELECT c.*, 
                   COUNT(p.id) as product_count,
                   (SELECT image FROM products WHERE category_id = c.id AND image IS NOT NULL AND image != '' LIMIT 1) as category_image
            FROM categories c 
            LEFT JOIN products p ON p.category_id = c.id AND p.active = 1
            WHERE c.active = 1 
            GROUP BY c.id 
            ORDER BY c.display_order
        ");

        $featured = Database::fetchAll("
            SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON c.id = p.category_id 
            WHERE p.active = 1 AND p.is_combo = 1 
            ORDER BY p.display_order 
            LIMIT 4
        ");

        view('public/home', [
            'categories' => $categories,
            'featured' => $featured,
        ]);
    }
}
