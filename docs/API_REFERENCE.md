# Puno at Halaman AI - REST API Reference Manual

All REST API endpoints accept and return JSON responses unless specified otherwise.

---

## 1. AI Plant Identification Endpoints

### `POST /api/identify`
Identifies a plant from a single uploaded image.

* **Content-Type**: `multipart/form-data`
* **Form Parameters**:
  - `image` (file, required): JPG, PNG, or WEBP photograph (max 10MB).
  - `image_type` (string, optional): `leaf`, `flower`, `bark`, `fruit`, `whole_plant`, `screenshot`.
  - `location_found` (string, optional): `forest`, `farm`, `garden`, `roadside`, `coastal`.
  - `province` (string, optional): Province name.
  - `has_flowers` (string, optional): `yes`, `no`.
  - `milky_sap` (string, optional): `yes`, `no`.
* **Response Example**:
```json
{
  "status": "identified",
  "primary_candidate": {
    "scientific_name": "Lagerstroemia speciosa",
    "common_name": "Banaba",
    "family": "Lythraceae",
    "genus": "Lagerstroemia",
    "species": "speciosa",
    "confidence": 86.0
  },
  "confidence_score": 86.0,
  "confidence_level": "High",
  "reasoning_summary": "Image exhibits characteristic opposite leaf arrangement...",
  "philippine_context": {
    "native_status": "NATIVE",
    "distribution": "Luzon, Visayas, Mindanao",
    "habitat": "Lowland secondary forests"
  },
  "safety": {
    "safety_category": "SAFE_FOR_GENERAL_CONTACT"
  },
  "identification_id": 1,
  "request_id": 1
}
```

### `POST /api/identify/multiple`
Identifies a plant by combining evidence from multiple uploaded photographs.

* **Content-Type**: `multipart/form-data`
* **Form Parameters**:
  - `images[]` (files, required): Array of image files.
  - `image_types[]` (array, optional): `['leaf', 'bark', 'flower']`.

---

## 2. Species Directory Endpoints

### `GET /api/plants`
Retrieves list of registered plant species.

### `GET /api/plants/search?q={query}`
Searches species by scientific name, common name, or local alias.

### `GET /api/plants/{id}`
Retrieves detailed botanical, medicinal, safety, and source profile for a plant ID.

---

## 3. Field Observation Endpoints

### `GET /api/observations`
Retrieves recent field observation records.

### `POST /api/observations`
Creates a new field observation record.
* **Form Parameters**:
  - `observer_name` (string, required)
  - `observation_date` (date, required)
  - `province` (string, required)
  - `municipality` (string, required)
  - `barangay` (string, optional)
  - `latitude` (float, optional)
  - `longitude` (float, optional)
  - `plant_height_m` (float, optional)
  - `estimated_dbh_cm` (float, optional)
  - `notes` (string, optional)

### `GET /api/observations/export?format={csv|json}`
Downloads complete observation dataset as a CSV or JSON file export.

---

## 4. Authentication & Admin Endpoints

### `POST /api/auth/login`
Authenticates user / administrator.

### `POST /api/auth/logout`
Destroys session and logs out.

### `POST /api/admin/plants` (Authenticated)
Creates a new plant species entry in the database.
