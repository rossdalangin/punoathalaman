# Standard Operating Procedure: Field Observation & Biodiversity Logging

**SOP Code:** SOP-PH-001
**Version:** 1.0
**Target Audience:** Foresters, Field Technicians, Environmental Volunteers, Community Researchers

---

## 1. Objective
To standardize field botanical data collection using "Puno at Halaman AI" to ensure accuracy, spatial precision, and compatibility with Philippine biodiversity reporting standards.

## 2. Equipment Required
- Mobile Smartphone with camera & GPS enabled.
- Measuring tape or DBH (Diameter at Breast Height) tape.
- Puno at Halaman AI Mobile Web App access (`/observations`).

## 3. Step-by-Step Procedure

### Step 3.1: Specimen Photography
1. Take one clear photo of a mature leaf (upper side).
2. Take one photo of the leaf underside showing venation.
3. Photograph tree bark, flowers, or fruits if present.
4. Photograph the overall plant growth habit.

### Step 3.2: AI Vision Pre-Analysis
1. Upload photos to "Puno at Halaman AI".
2. Note the AI candidate species and confidence score.
3. Review "How to Verify" diagnostic checklists.

### Step 3.3: Field Data Logging
1. Open Field Records (`/observations`).
2. Fill in:
   - **Observer Name & Date**
   - **Province, Municipality, Barangay**
   - **Habitat Type** (e.g., Lowland Dipterocarp Forest, Riparian)
   - **GPS Coordinates** (Latitude & Longitude)
   - **Plant Height (m)** and **Estimated DBH (cm)**
   - **Field Notes** (phenology, associated species, threats)
3. Click **Save Field Observation Record**.

### Step 3.4: Data Exporting
- Weekly/monthly exports should be downloaded as CSV/JSON via `/api/observations/export?format=csv` for institutional reporting.
