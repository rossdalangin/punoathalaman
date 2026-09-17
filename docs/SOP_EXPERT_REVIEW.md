# Standard Operating Procedure: Expert Review & Verification Workflow

**SOP Code:** SOP-PH-002
**Version:** 1.0
**Target Audience:** Expert Botanists, Licensed Foresters, DENR Panel Reviewers

---

## 1. Objective
To govern the review and validation of low-confidence or ambiguous plant identification requests submitted by field users.

## 2. Review Queue Management
1. Access Admin Dashboard (`/admin`).
2. Filter requests by status: `PENDING_REVIEW`.
3. Review submitted user photos, locality data, and preliminary AI reasoning summary.

## 3. Evaluation Criteria
An identification is marked **VERIFIED** if:
- Visible diagnostic markers (leaf margins, apex, venation, floral structure) conclusively match herbarium specimen records or published flora keys.
- Geographic locality matches known species distribution in the Philippines.

An identification is marked **REJECTED / UNRESOLVED** if:
- Image quality is insufficient to distinguish from look-alike species.
- Essential structures (flowers/fruit) are absent for cryptic genera.

## 4. Action Steps
1. If verified: Update status to `VERIFIED` and select validated species ID in database.
2. Add expert notes detailing key diagnostic features observed.
3. If unverified: Request additional field photos (bark/leaf underside/fruit) from user.
