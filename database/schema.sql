-- Schema for Puno at Halaman AI Database

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'expert', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    scientific_name VARCHAR(255) NOT NULL UNIQUE,
    primary_common_name VARCHAR(255) NOT NULL,
    kingdom VARCHAR(100) DEFAULT 'Plantae',
    family VARCHAR(100) NOT NULL,
    genus VARCHAR(100) NOT NULL,
    species VARCHAR(100) NOT NULL,
    native_status ENUM('NATIVE', 'ENDEMIC', 'INTRODUCED', 'NATURALIZED', 'INVASIVE', 'CULTIVATED', 'UNKNOWN') NOT NULL DEFAULT 'NATIVE',
    habitat TEXT,
    philippine_distribution TEXT,
    elevation_range VARCHAR(100) DEFAULT NULL,
    forest_type VARCHAR(100) DEFAULT NULL,
    leaf_type VARCHAR(100) DEFAULT NULL,
    leaf_arrangement VARCHAR(100) DEFAULT NULL,
    leaf_margin VARCHAR(100) DEFAULT NULL,
    leaf_apex VARCHAR(100) DEFAULT NULL,
    leaf_base VARCHAR(100) DEFAULT NULL,
    venation VARCHAR(100) DEFAULT NULL,
    growth_habit VARCHAR(100) DEFAULT NULL,
    bark_description TEXT,
    flower_description TEXT,
    fruit_description TEXT,
    distinctive_markings TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plant_names (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plant_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    name_type ENUM('english', 'tagalog', 'local', 'regional', 'alias') NOT NULL DEFAULT 'local',
    language_region VARCHAR(100) DEFAULT NULL,
    verified_status ENUM('verified', 'unverified') NOT NULL DEFAULT 'verified',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS identification_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    session_id VARCHAR(100) DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_answers_json JSON DEFAULT NULL,
    image_quality_rating VARCHAR(50) DEFAULT 'GOOD',
    quality_feedback TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plant_identifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT DEFAULT NULL,
    user_id INT DEFAULT NULL,
    session_id VARCHAR(100) DEFAULT NULL,
    primary_plant_id INT DEFAULT NULL,
    confidence_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    confidence_level ENUM('High', 'Moderate', 'Low') NOT NULL DEFAULT 'Low',
    reasoning_summary TEXT,
    raw_ai_response JSON DEFAULT NULL,
    verified_by_expert TINYINT(1) DEFAULT 0,
    expert_notes TEXT DEFAULT NULL,
    status ENUM('AI_IDENTIFIED', 'PENDING_REVIEW', 'EXPERT_REVIEWED', 'VERIFIED') NOT NULL DEFAULT 'AI_IDENTIFIED',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES identification_requests(id) ON DELETE SET NULL,
    FOREIGN KEY (primary_plant_id) REFERENCES plants(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS identification_candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identification_id INT NOT NULL,
    plant_id INT DEFAULT NULL,
    candidate_scientific_name VARCHAR(255) NOT NULL,
    candidate_common_name VARCHAR(255) NOT NULL,
    confidence_percentage DECIMAL(5,2) NOT NULL,
    distinction_notes TEXT DEFAULT NULL,
    rank_order INT DEFAULT 1,
    FOREIGN KEY (identification_id) REFERENCES plant_identifications(id) ON DELETE CASCADE,
    FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plant_observations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    identification_id INT DEFAULT NULL,
    plant_id INT DEFAULT NULL,
    observation_code VARCHAR(50) NOT NULL UNIQUE,
    observer_name VARCHAR(255) DEFAULT 'Anonymous Observer',
    observation_date DATE NOT NULL,
    province VARCHAR(100) NOT NULL,
    municipality VARCHAR(100) NOT NULL,
    barangay VARCHAR(100) DEFAULT NULL,
    location_description TEXT DEFAULT NULL,
    latitude DECIMAL(10, 8) DEFAULT NULL,
    longitude DECIMAL(11, 8) DEFAULT NULL,
    habitat VARCHAR(255) DEFAULT NULL,
    plant_height_m DECIMAL(6,2) DEFAULT NULL,
    estimated_dbh_cm DECIMAL(6,2) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    additional_observations TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (identification_id) REFERENCES plant_identifications(id) ON DELETE SET NULL,
    FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plant_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plant_id INT DEFAULT NULL,
    observation_id INT DEFAULT NULL,
    request_id INT DEFAULT NULL,
    file_path VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    image_type ENUM('leaf', 'bark', 'flower', 'fruit', 'whole_plant', 'screenshot', 'observation') NOT NULL DEFAULT 'leaf',
    mime_type VARCHAR(100) NOT NULL DEFAULT 'image/jpeg',
    file_size INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE CASCADE,
    FOREIGN KEY (observation_id) REFERENCES plant_observations(id) ON DELETE CASCADE,
    FOREIGN KEY (request_id) REFERENCES identification_requests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plant_uses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plant_id INT NOT NULL,
    use_category ENUM('ecological', 'agricultural', 'traditional', 'scientific') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    evidence_level ENUM('ESTABLISHED', 'SUPPORTED_BY_SOME_RESEARCH', 'PRELIMINARY_EVIDENCE', 'TRADITIONAL_USE_ONLY', 'INSUFFICIENT_EVIDENCE') NOT NULL DEFAULT 'TRADITIONAL_USE_ONLY',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plant_medicinal_information (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plant_id INT NOT NULL UNIQUE,
    is_recognized_medicinal ENUM('YES', 'TRADITIONALLY_USED', 'POTENTIAL', 'NO_RELIABLE_USE', 'UNKNOWN') NOT NULL DEFAULT 'TRADITIONALLY_USED',
    traditional_uses_text TEXT,
    scientific_evidence_text TEXT,
    active_compounds TEXT,
    known_risks TEXT,
    known_interactions TEXT,
    toxic_parts TEXT,
    preparation_risks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plant_safety (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plant_id INT NOT NULL UNIQUE,
    safety_category ENUM('SAFE_FOR_GENERAL_CONTACT', 'CAUTION', 'POTENTIALLY_TOXIC', 'KNOWN_POISONOUS_PLANT', 'UNKNOWN') NOT NULL DEFAULT 'SAFE_FOR_GENERAL_CONTACT',
    primary_warning TEXT,
    toxic_parts TEXT,
    look_alike_species TEXT,
    look_alike_distinction TEXT,
    warning_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plant_conservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plant_id INT NOT NULL UNIQUE,
    iucn_status VARCHAR(100) DEFAULT 'Least Concern (LC)',
    denr_status VARCHAR(100) DEFAULT 'Not Listed',
    threatened_status VARCHAR(100) DEFAULT 'Not Threatened',
    protected_status VARCHAR(100) DEFAULT 'Not Protected',
    cites_status VARCHAR(100) DEFAULT 'Not Listed',
    collection_restrictions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plant_sources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plant_id INT DEFAULT NULL,
    fact_type VARCHAR(100) NOT NULL DEFAULT 'general',
    source_name VARCHAR(255) NOT NULL,
    source_url VARCHAR(500) DEFAULT NULL,
    source_type ENUM('government', 'academic', 'database', 'publication', 'herbarium') NOT NULL DEFAULT 'database',
    publication VARCHAR(255) DEFAULT NULL,
    author VARCHAR(255) DEFAULT NULL,
    publication_date VARCHAR(50) DEFAULT NULL,
    species_reference VARCHAR(255) DEFAULT NULL,
    evidence_type VARCHAR(100) DEFAULT 'Peer-reviewed research / Government database',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    target_type VARCHAR(100) DEFAULT NULL,
    target_id INT DEFAULT NULL,
    details_json JSON DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
