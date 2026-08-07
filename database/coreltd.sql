-- ============================================================
-- CORE LTD DATABASE
-- ============================================================

CREATE DATABASE IF NOT EXISTS coreltd
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE coreltd;


-- ============================================================
-- 1. ADMINS / USERS
-- ============================================================

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','manager','staff') DEFAULT 'staff',
    status TINYINT(1) DEFAULT 1,
    last_login DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ============================================================
-- 2. CATEGORIES
-- ============================================================

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id INT UNSIGNED DEFAULT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    status TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_category_parent
        FOREIGN KEY (parent_id)
        REFERENCES categories(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;


-- ============================================================
-- 3. BRANDS
-- ============================================================

CREATE TABLE brands (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    logo VARCHAR(255) DEFAULT NULL,
    website VARCHAR(255) DEFAULT NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ============================================================
-- 4. PRODUCTS
-- ============================================================

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    category_id INT UNSIGNED DEFAULT NULL,
    brand_id INT UNSIGNED DEFAULT NULL,

    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,

    sku VARCHAR(100) DEFAULT NULL UNIQUE,

    short_description TEXT DEFAULT NULL,
    description LONGTEXT DEFAULT NULL,

    price DECIMAL(12,2) DEFAULT 0.00,
    old_price DECIMAL(12,2) DEFAULT NULL,

    cost_price DECIMAL(12,2) DEFAULT NULL,

    stock_quantity INT DEFAULT 0,
    low_stock_level INT DEFAULT 5,

    main_image VARCHAR(255) DEFAULT NULL,

    featured TINYINT(1) DEFAULT 0,
    status TINYINT(1) DEFAULT 1,

    views INT UNSIGNED DEFAULT 0,

    meta_title VARCHAR(255) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    meta_keywords TEXT DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_product_category
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_product_brand
        FOREIGN KEY (brand_id)
        REFERENCES brands(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 5. PRODUCT IMAGES
-- ============================================================

CREATE TABLE product_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    product_id INT UNSIGNED NOT NULL,

    image VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) DEFAULT NULL,

    sort_order INT DEFAULT 0,
    is_primary TINYINT(1) DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_product_images_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 6. PRODUCT FEATURES
-- ============================================================

CREATE TABLE product_features (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    product_id INT UNSIGNED NOT NULL,

    feature TEXT NOT NULL,
    sort_order INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_product_features_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 7. PRODUCT SPECIFICATIONS
-- ============================================================

CREATE TABLE product_specifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    product_id INT UNSIGNED NOT NULL,

    specification_group VARCHAR(150) DEFAULT NULL,
    specification_name VARCHAR(150) NOT NULL,
    specification_value TEXT NOT NULL,

    sort_order INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_product_specs_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 8. PRODUCT TAGS
-- ============================================================

CREATE TABLE tags (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- ============================================================
-- 9. PRODUCT ↔ TAGS
-- ============================================================

CREATE TABLE product_tags (
    product_id INT UNSIGNED NOT NULL,
    tag_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (product_id, tag_id),

    CONSTRAINT fk_product_tags_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_product_tags_tag
        FOREIGN KEY (tag_id)
        REFERENCES tags(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 10. PRODUCT VARIANTS
-- ============================================================

CREATE TABLE product_variants (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    product_id INT UNSIGNED NOT NULL,

    variant_name VARCHAR(150) NOT NULL,
    sku VARCHAR(100) DEFAULT NULL UNIQUE,

    price DECIMAL(12,2) DEFAULT NULL,
    stock_quantity INT DEFAULT 0,

    image VARCHAR(255) DEFAULT NULL,

    status TINYINT(1) DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_variant_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 11. PRODUCT STOCK MOVEMENTS
-- ============================================================

CREATE TABLE stock_movements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    product_id INT UNSIGNED NOT NULL,

    movement_type ENUM(
        'purchase',
        'sale',
        'adjustment',
        'return',
        'damage'
    ) NOT NULL,

    quantity INT NOT NULL,

    previous_quantity INT DEFAULT 0,
    new_quantity INT DEFAULT 0,

    reference VARCHAR(150) DEFAULT NULL,
    notes TEXT DEFAULT NULL,

    created_by INT UNSIGNED DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_stock_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_stock_user
        FOREIGN KEY (created_by)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 12. PRODUCT REVIEWS
-- ============================================================

CREATE TABLE reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    product_id INT UNSIGNED NOT NULL,

    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(150) DEFAULT NULL,

    rating TINYINT UNSIGNED NOT NULL,
    review TEXT NOT NULL,

    status ENUM('pending','approved','rejected')
        DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_review_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 13. CUSTOMER QUOTES / INQUIRIES
-- ============================================================

CREATE TABLE inquiries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(150) DEFAULT NULL,
    customer_phone VARCHAR(50) DEFAULT NULL,

    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,

    product_id INT UNSIGNED DEFAULT NULL,

    status ENUM(
        'new',
        'contacted',
        'processing',
        'completed',
        'cancelled'
    ) DEFAULT 'new',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_inquiry_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 14. QUOTE ITEMS
-- ============================================================

CREATE TABLE quote_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    inquiry_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,

    quantity INT UNSIGNED DEFAULT 1,

    price DECIMAL(12,2) DEFAULT 0.00,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_quote_item_inquiry
        FOREIGN KEY (inquiry_id)
        REFERENCES inquiries(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_quote_item_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 15. WISHLIST
-- ============================================================

CREATE TABLE wishlists (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    session_id VARCHAR(255) DEFAULT NULL,
    customer_email VARCHAR(150) DEFAULT NULL,

    product_id INT UNSIGNED NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_wishlist_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 16. SITE SETTINGS
-- ============================================================

CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    setting_key VARCHAR(150) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- ============================================================
-- 17. SEO REDIRECTS
-- ============================================================

CREATE TABLE redirects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    old_url VARCHAR(255) NOT NULL UNIQUE,
    new_url VARCHAR(255) NOT NULL,

    redirect_type SMALLINT DEFAULT 301,

    status TINYINT(1) DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- ============================================================
-- 18. CONTACT MESSAGES
-- ============================================================

CREATE TABLE contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(50) DEFAULT NULL,

    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,

    status ENUM('new','read','replied','closed')
        DEFAULT 'new',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- ============================================================
-- 19. ADMIN ACTIVITY LOG
-- ============================================================

CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED DEFAULT NULL,

    action VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,

    ip_address VARCHAR(45) DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_activity_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- DEFAULT CATEGORIES
-- ============================================================

INSERT INTO categories
(name, slug, description)
VALUES

('Networking', 'networking',
 'Networking equipment including routers, switches and wireless devices'),

('Routers', 'routers',
 'Wireless and wired routers'),

('Switches', 'switches',
 'Network switches and Ethernet switches'),

('Access Points', 'access-points',
 'Indoor and outdoor wireless access points'),

('Fiber Optics', 'fiber-optics',
 'Fiber optic networking equipment and accessories'),

('CCTV & Security', 'cctv-security',
 'CCTV cameras, DVRs, NVRs and security equipment'),

('Tools', 'tools',
 'Networking and fiber installation tools'),

('Accessories', 'accessories',
 'Networking accessories and installation accessories');


-- ============================================================
-- DEFAULT BRANDS
-- ============================================================

INSERT INTO brands
(name, slug)
VALUES

('MikroTik', 'mikrotik'),
('Tenda', 'tenda'),
('TP-Link', 'tp-link'),
('Ubiquiti', 'ubiquiti'),
('Ruijie', 'ruijie'),
('Reyee', 'reyee'),
('D-Link', 'd-link'),
('Huawei', 'huawei'),
('Signal Fire', 'signal-fire'),
('Hioso', 'hioso');


-- ============================================================
-- DEFAULT SITE SETTINGS
-- ============================================================

INSERT INTO settings
(setting_key, setting_value)
VALUES

('site_name', 'CORE LTD'),
('site_description', 'Networking, Fiber, CCTV and IT Solutions'),
('site_phone', ''),
('site_email', ''),
('site_address', ''),
('whatsapp_number', ''),
('currency', 'KES'),
('currency_symbol', 'KSh'),
('facebook_url', ''),
('instagram_url', ''),
('youtube_url', ''),
('tiktok_url', ''),
('site_logo', ''),
('site_favicon', '');


-- ============================================================
-- INDEXES
-- ============================================================

CREATE INDEX idx_products_category
ON products(category_id);

CREATE INDEX idx_products_brand
ON products(brand_id);

CREATE INDEX idx_products_status
ON products(status);

CREATE INDEX idx_products_featured
ON products(featured);

CREATE INDEX idx_products_name
ON products(name);

CREATE INDEX idx_products_created
ON products(created_at);

CREATE INDEX idx_inquiries_status
ON inquiries(status);

CREATE INDEX idx_reviews_product
ON reviews(product_id);
