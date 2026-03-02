-- Database indexing suggestions for performance
-- Run if not already present

ALTER TABLE products ADD INDEX idx_products_slug (slug);
ALTER TABLE products ADD INDEX idx_products_featured_created (featured, created_at);
ALTER TABLE products ADD INDEX idx_products_category_featured (category_id, featured);
ALTER TABLE orders ADD INDEX idx_orders_user_status (user_id, status);
ALTER TABLE orders ADD INDEX idx_orders_created (created_at);
ALTER TABLE order_items ADD INDEX idx_order_items_order (order_id);
ALTER TABLE wishlist ADD INDEX idx_wishlist_user (user_id);
