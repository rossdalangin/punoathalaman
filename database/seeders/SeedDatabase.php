<?php

namespace Database\Seeders;

use App\Helpers\Database;
use PDO;

class SeedDatabase
{
    public static function run(): void
    {
        $pdo = Database::getConnection();

        // 1. Seed Users (Admin & Experts)
        $pdo->exec("DELETE FROM users;");
        $adminPassword = password_hash('AdminSecret123!', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Dr. Maria Santos (Botanist)', 'admin@punoathalaman.ph', $adminPassword, 'admin']);
        $stmt->execute(['Juan dela Cruz (Forester)', 'forester@punoathalaman.ph', $adminPassword, 'expert']);

        // 2. Seed Plants
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("TRUNCATE TABLE plant_sources;");
        $pdo->exec("TRUNCATE TABLE plant_conservation;");
        $pdo->exec("TRUNCATE TABLE plant_safety;");
        $pdo->exec("TRUNCATE TABLE plant_medicinal_information;");
        $pdo->exec("TRUNCATE TABLE plant_uses;");
        $pdo->exec("TRUNCATE TABLE plant_images;");
        $pdo->exec("TRUNCATE TABLE plant_names;");
        $pdo->exec("TRUNCATE TABLE plants;");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        $plantsData = [
            [
                'scientific_name' => 'Lagerstroemia speciosa',
                'primary_common_name' => 'Banaba',
                'kingdom' => 'Plantae',
                'family' => 'Lythraceae',
                'genus' => 'Lagerstroemia',
                'species' => 'speciosa',
                'native_status' => 'NATIVE',
                'habitat' => 'Lowland secondary forests, riverbanks, open areas, and urban gardens.',
                'philippine_distribution' => 'Widespread across Luzon, Visayas, and Mindanao (e.g., Laguna, Quezon, Bicol, Cebu, Davao).',
                'elevation_range' => '0 - 800 m above sea level',
                'forest_type' => 'Lowland dipterocarp & secondary forests',
                'leaf_type' => 'Simple, thick, elliptic to oblong',
                'leaf_arrangement' => 'Opposite or near-opposite',
                'leaf_margin' => 'Entire',
                'leaf_apex' => 'Acute to acuminate',
                'leaf_base' => 'Obtuse to rounded',
                'venation' => 'Pinnate with prominent lateral veins',
                'growth_habit' => 'Medium-sized deciduous tree up to 20 meters tall',
                'bark_description' => 'Smooth, gray to light brown, flaking off in thin strips.',
                'flower_description' => 'Showy pink to purple petals with crinkled edges in large terminal panicles.',
                'fruit_description' => 'Woody capsule, subglobose, containing winged seeds.',
                'distinctive_markings' => 'Reddish leaves before shedding; crinkled purple/pink petals.',
                'images' => [
                    ['file_path' => 'assets/images/species/banaba_leaf.jpg', 'filename' => 'banaba_leaf.jpg', 'type' => 'leaf'],
                    ['file_path' => 'assets/images/species/banaba_flower.jpg', 'filename' => 'banaba_flower.jpg', 'type' => 'flower'],
                    ['file_path' => 'assets/images/species/banaba_tree.jpg', 'filename' => 'banaba_tree.jpg', 'type' => 'whole_plant']
                ],
                'names' => [
                    ['name' => 'Banaba', 'type' => 'tagalog', 'region' => 'Luzon/Tagalog'],
                    ['name' => 'Pride of India', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Gaur', 'type' => 'local', 'region' => 'Ilocos'],
                    ['name' => 'Mitla', 'type' => 'local', 'region' => 'Pampanga'],
                    ['name' => 'Dugom', 'type' => 'regional', 'region' => 'Visayas']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Urban Greening & Shade', 'description' => 'Provides urban canopy cover, supports pollinators during flowering season, and stabilizes riverbanks.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Ornamental & Timber', 'description' => 'Widely planted as an ornamental tree in roadsides and parks; wood used for construction and agricultural tools.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Traditional Herbal Tea', 'description' => 'Leaves decoction traditionally consumed by Philippine communities for kidney health and urinary health.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Corosolic Acid & Blood Glucose Research', 'description' => 'Contains corosolic acid and ellagitannins. In-vitro and preliminary studies suggest potential insulin-like effects, but clinical dietary caution is advised.', 'evidence' => 'SUPPORTED_BY_SOME_RESEARCH']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'YES',
                    'traditional_uses_text' => 'Leaf decoction traditionally used for kidney and urinary bladder ailments, fever, and metabolic support.',
                    'scientific_evidence_text' => 'Department of Health (DOH) PITAHC endorsed medicinal plant. Corosolic acid demonstrates alpha-glucosidase inhibition in phytochemical research.',
                    'active_compounds' => 'Corosolic acid, ellagitannins (lagerstroemin), gallic acid, valoneic acid.',
                    'known_risks' => 'Hypoglycemia risk when combined with prescription antidiabetic medications.',
                    'known_interactions' => 'May enhance insulin sensitivity; consult a physician if taking hypoglycemic drugs.',
                    'toxic_parts' => 'None documented under standard preparation.',
                    'preparation_risks' => 'Improper boiling or excessive consumption without hydration may affect kidney load.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'Do not replace prescribed diabetes or kidney medications with Banaba tea without consulting a physician.',
                    'toxic_parts' => 'None known for leaves.',
                    'look_alike_species' => 'Lagerstroemia indica (Crape Myrtle)',
                    'look_alike_distinction' => 'Lagerstroemia indica is a smaller shrub with much smaller leaves (2-7cm) compared to L. speciosa (10-25cm).',
                    'warning_text' => 'CONFIRM LEAF SIZE AND TREE HABIT BEFORE EXTRACT PREPARATION.'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Threatened',
                    'threatened_status' => 'Safe',
                    'protected_status' => 'Cultivated & Protected in watersheds',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'Pruning allowed in cultivated areas; wild gathering in forest reserves restricted.'
                ],
                'sources' => [
                    ['name' => 'DENR Biodiversity Management Bureau', 'url' => 'https://bmb.gov.ph', 'type' => 'government'],
                    ['name' => 'Philippine Institute of Traditional and Alternative Health Care (PITAHC)', 'url' => 'https://pitahc.gov.ph', 'type' => 'government'],
                    ['name' => 'Plants of the World Online (POWO - Kew)', 'url' => 'https://powo.science.kew.org/taxon/urn:lsid:ipni.org:names:553655-1', 'type' => 'database']
                ]
            ],
            [
                'scientific_name' => 'Blumea balsamifera',
                'primary_common_name' => 'Sambong',
                'kingdom' => 'Plantae',
                'family' => 'Asteraceae',
                'genus' => 'Blumea',
                'species' => 'balsamifera',
                'native_status' => 'NATIVE',
                'habitat' => 'Grasslands, open fields, clearings, waste areas, and hill slopes.',
                'philippine_distribution' => 'Abundant throughout the entire Philippines archipelago.',
                'elevation_range' => '0 - 1500 m above sea level',
                'forest_type' => 'Secondary scrub, savannah, and degraded forest openings',
                'leaf_type' => 'Simple, oblong to lanceolate, densely velvety hairy',
                'leaf_arrangement' => 'Alternate',
                'leaf_margin' => 'Toothed or serrate, often appendaged at leaf base',
                'leaf_apex' => 'Acute',
                'leaf_base' => 'Attenuate with small appendages',
                'venation' => 'Pinnate',
                'growth_habit' => 'Coarse, aromatic, half-woody herb/shrub up to 3 meters tall',
                'bark_description' => 'Greenish to woody stem covered in dense silvery hairs.',
                'flower_description' => 'Yellow head flowers grouped in dense terminal panicles.',
                'fruit_description' => 'Small achenes with hair-like pappus for wind dispersal.',
                'distinctive_markings' => 'Strong camphor aromatic smell when leaves are crushed; soft velvety hairy texture.',
                'images' => [
                    ['file_path' => 'assets/images/species/sambong_leaf.jpg', 'filename' => 'sambong_leaf.jpg', 'type' => 'leaf'],
                    ['file_path' => 'assets/images/species/sambong_flower.jpg', 'filename' => 'sambong_flower.jpg', 'type' => 'flower']
                ],
                'names' => [
                    ['name' => 'Sambong', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Blumea Camphor', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Subusub', 'type' => 'local', 'region' => 'Ilocos'],
                    ['name' => 'Gabon', 'type' => 'regional', 'region' => 'Visayas'],
                    ['name' => 'Ayoban', 'type' => 'regional', 'region' => 'Bicol/Visayas']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Pioneer Grassland Shrub', 'description' => 'Colonizes degraded land, helps prevent soil erosion, and provides habitat for grassland insects.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Companion Crop & Bio-pesticide', 'description' => 'Aromatic essential oils act as natural insect repellent in small farms.', 'evidence' => 'SUPPORTED_BY_SOME_RESEARCH'],
                    ['category' => 'traditional', 'title' => 'Diuretic & Postpartum Bath', 'description' => 'Boiled leaves used for body warming, headache relief, and kidney stone passage.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Diuretic & Anti-urolithiasis Activity', 'description' => 'DOH-PITAHC approved for renal calculi dissolution and as a diuretic agent.', 'evidence' => 'ESTABLISHED']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'YES',
                    'traditional_uses_text' => 'Tradition dictates boiling leaves for kidney stone dissolution, colds, fever, and hypertension.',
                    'scientific_evidence_text' => 'Officially recognized by DOH and PITAHC as a clinically proven herbal drug for anti-urolithiasis (kidney stones) and diuretic treatment.',
                    'active_compounds' => 'L-camphor, L-borneol, flavonoids (blumeatin), sesquiterpenes.',
                    'known_risks' => 'Allergic contact dermatitis in sensitive individuals.',
                    'known_interactions' => 'May potentiate synthetic diuretic drugs; monitor urine output.',
                    'toxic_parts' => 'Essential oil in pure concentrated forms is an irritant.',
                    'preparation_risks' => 'Do not boil in aluminum containers; use glass, stainless steel, or clay pots.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'Ensure kidney stones are not causing acute urinary obstruction before self-medicating. Consult a physician for acute pain.',
                    'toxic_parts' => 'Concentrated volatile oils.',
                    'look_alike_species' => 'Pluchea indica (Kalapini)',
                    'look_alike_distinction' => 'Pluchea indica leaves smell pungent/marshy rather than sweet camphor; found primarily in mangrove coastal margins.',
                    'warning_text' => 'CRUSH LEAF TO VERIFY DISTINCTIVE CAMPHOR AROMA.'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Listed',
                    'threatened_status' => 'Safe',
                    'protected_status' => 'Common / Abundant',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'None; widely cultivated in herbal gardens.'
                ],
                'sources' => [
                    ['name' => 'Department of Health (DOH) Philippines - Traditional Medicine Program', 'url' => 'https://doh.gov.ph', 'type' => 'government'],
                    ['name' => 'PITAHC Herbal Pharmacopeia', 'url' => 'https://pitahc.gov.ph', 'type' => 'government']
                ]
            ],
            [
                'scientific_name' => 'Vitex negundo',
                'primary_common_name' => 'Lagundi',
                'kingdom' => 'Plantae',
                'family' => 'Lamiaceae',
                'genus' => 'Vitex',
                'species' => 'negundo',
                'native_status' => 'NATIVE',
                'habitat' => 'Thickets, waste lands, roadsides, riverbanks, and coastal thickets.',
                'philippine_distribution' => 'Common across Luzon, Mindoro, Palawan, Visayas, and Mindanao.',
                'elevation_range' => '0 - 1000 m above sea level',
                'forest_type' => 'Secondary thickets and coastal shrublands',
                'leaf_type' => 'Palmateland compound with 3 to 5 linear-lanceolate leaflets',
                'leaf_arrangement' => 'Opposite',
                'leaf_margin' => 'Entire or slightly wavy/toothed',
                'leaf_apex' => 'Acuminate',
                'leaf_base' => 'Cuneate',
                'venation' => 'Pinnate on each leaflet',
                'growth_habit' => 'Erect branched shrub or small tree 2-5 meters tall',
                'bark_description' => 'Light gray to reddish-brown, smooth or shallowly fissured.',
                'flower_description' => 'Small bluish-purple to pale violet flowers arranged in branched terminal thyrses.',
                'fruit_description' => 'Small succulent black or purple drupe, subglobose.',
                'distinctive_markings' => 'Palmate 3-5 leaflet arrangement with whitish hairy underside and aromatic crushed leaves.',
                'images' => [
                    ['file_path' => 'assets/images/species/lagundi_leaf.jpg', 'filename' => 'lagundi_leaf.jpg', 'type' => 'leaf'],
                    ['file_path' => 'assets/images/species/lagundi_flower.jpg', 'filename' => 'lagundi_flower.jpg', 'type' => 'flower']
                ],
                'names' => [
                    ['name' => 'Lagundi', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Five-leaved Chaste Tree', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Dangla', 'type' => 'local', 'region' => 'Ilocos'],
                    ['name' => 'Sagarai', 'type' => 'local', 'region' => 'Cagayan'],
                    ['name' => 'Ligas', 'type' => 'regional', 'region' => 'Pampanga']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Soil Binder & Pollinator Plant', 'description' => 'Prevents riverbank erosion and provides year-round nectar for native bees.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Grain Storage Insect Repellent', 'description' => 'Leaves mixed with stored rice grains repel rice weevils.', 'evidence' => 'SUPPORTED_BY_SOME_RESEARCH'],
                    ['category' => 'traditional', 'title' => 'Asthma & Cough Bath', 'description' => 'Leaf decoction traditionally taken for bronchitis, fever, and muscle pain relief.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Bronchodilator & Anti-asthmatic', 'description' => 'DOH-PITAHC approved medicine for cough and mild asthma symptom relief.', 'evidence' => 'ESTABLISHED']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'YES',
                    'traditional_uses_text' => 'Boiled leaves used for coughs, asthma, fever, rheumatism, and insect bites.',
                    'scientific_evidence_text' => 'Clinically validated by the Philippine National Integrated Research Program on Medicinal Plants (NIRPROMP) for bronchodilator and antihistamine efficacy.',
                    'active_compounds' => 'Chrysoplenol D, casticin, sabinene, iridoid glycosides, luteolin.',
                    'known_risks' => 'Excessive doses may cause mild stomach upset or nausea.',
                    'known_interactions' => 'No severe drug interactions reported under standard therapeutic doses.',
                    'toxic_parts' => 'None under normal leaf decoction usage.',
                    'preparation_risks' => 'Wash leaves thoroughly to remove pest debris.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'Severe asthma attacks require immediate hospital emergency care, not reliance on herbal tea alone.',
                    'toxic_parts' => 'None documented.',
                    'look_alike_species' => 'Vitex trifolia (Lagunding-dagat)',
                    'look_alike_distinction' => 'Vitex trifolia usually has only 3 leaflets (trifoliate) and rounded leaflet apices, growing strictly along beach shores.',
                    'warning_text' => 'COUNT LEAFLETS (LAGUNDI HAS 5 LEAFLETS MOSTLY).'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Threatened',
                    'threatened_status' => 'Safe',
                    'protected_status' => 'Commonly cultivated',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'Abundant in wild and domestic gardens.'
                ],
                'sources' => [
                    ['name' => 'NIRPROMP - University of the Philippines Manila', 'url' => 'https://upm.edu.ph', 'type' => 'academic'],
                    ['name' => 'DOH PITAHC Approved Medicinal Plants', 'url' => 'https://pitahc.gov.ph', 'type' => 'government']
                ]
            ],
            [
                'scientific_name' => 'Pterocarpus indicus',
                'primary_common_name' => 'Narra',
                'kingdom' => 'Plantae',
                'family' => 'Fabaceae',
                'genus' => 'Pterocarpus',
                'species' => 'indicus',
                'native_status' => 'NATIVE',
                'habitat' => 'Primary and secondary lowland forests, along rivers, and coastal margins.',
                'philippine_distribution' => 'Luzon, Mindoro, Palawan, Samar, Leyte, Negros, Mindanao.',
                'elevation_range' => '0 - 600 m above sea level',
                'forest_type' => 'Lowland dipterocarp and riparian forest',
                'leaf_type' => 'Pinnately compound with 7-11 alternate ovate leaflets',
                'leaf_arrangement' => 'Alternate',
                'leaf_margin' => 'Entire',
                'leaf_apex' => 'Acuminate',
                'leaf_base' => 'Obtuse to rounded',
                'venation' => 'Pinnate',
                'growth_habit' => 'Large timber tree up to 30-35 meters tall with wide spreading crown and buttress roots',
                'bark_description' => 'Flaky, grayish-brown, exudes red crimson sap when slashed.',
                'flower_description' => 'Fragrant yellow panicles blooming profusely in short bursts during early dry season.',
                'fruit_description' => 'Disc-like orbicular pod with a central woody seed surrounded by a papery wing (samara).',
                'distinctive_markings' => 'Red liquid exuded from cut bark (dragon blood sap); winged disc fruit pods; national tree of the Philippines.',
                'images' => [
                    ['file_path' => 'assets/images/species/narra_tree.jpg', 'filename' => 'narra_tree.jpg', 'type' => 'whole_plant'],
                    ['file_path' => 'assets/images/species/narra_leaf.jpg', 'filename' => 'narra_leaf.jpg', 'type' => 'leaf']
                ],
                'names' => [
                    ['name' => 'Narra', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Pterocarpus / Malay Padauk', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Asana', 'type' => 'tagalog', 'region' => 'Southern Luzon'],
                    ['name' => 'Vitali', 'type' => 'local', 'region' => 'Zamboanga'],
                    ['name' => 'Dungan', 'type' => 'regional', 'region' => 'Mindanao']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Nitrogen Fixer & Keystone Forest Tree', 'description' => 'Fixes atmospheric nitrogen via rhizobia symbiosis, enriches soil, and forms dense forest canopy.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'High-Grade Timber & Reforestation', 'description' => 'Famous durable hardwood used in fine furniture; vital native species for watershed restoration.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Red Sap Tonic', 'description' => 'Red resin exudate traditionally applied for mouth sores and throat inflammation.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Antioxidant Bark Polyphenols', 'description' => 'Wood and bark extracts contain pterocarpans and polyphenols under preliminary pharmacological study.', 'evidence' => 'PRELIMINARY_EVIDENCE']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'TRADITIONALLY_USED',
                    'traditional_uses_text' => 'Decoction of bark or wood shavings traditionally used for mouth gargles, diarrhea, and general tonic.',
                    'scientific_evidence_text' => 'Rich in polyphenols and tannins showing antioxidant activity in vitro. Not an officially approved primary pharmaceutical drug.',
                    'active_compounds' => 'Pterocarpin, homopterocarpin, prunetin, narrative flavonoids.',
                    'known_risks' => 'High tannin content can cause gastric irritation if consumed excessively.',
                    'known_interactions' => 'Tannins may bind with dietary iron and mineral supplements.',
                    'toxic_parts' => 'Unfiltered concentrated bark extracts.',
                    'preparation_risks' => 'Do not strip living bark from wild trees as it causes fungal infection and tree mortality.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'PROTECTED SPECIES: Illegal logging or bark stripping in wild forests is strictly prohibited by DENR DAO 2017-11.',
                    'toxic_parts' => 'None in normal contact.',
                    'look_alike_species' => 'Pterocarpus santalinus (Red Sanders)',
                    'look_alike_distinction' => 'Red Sanders has trifoliate leaves (3 leaflets) compared to Narra (7-11 leaflets).',
                    'warning_text' => 'DO NOT COLLECT FROM WILD FORESTS. PHOTOGRAPH OBSERVATION ONLY.'
                ],
                'conservation' => [
                    'iucn_status' => 'Endangered (EN)',
                    'denr_status' => 'Vulnerable / Protected',
                    'threatened_status' => 'Threatened by over-exploitation',
                    'protected_status' => 'Strictly Protected Under Philippine Forestry Law',
                    'cites_status' => 'Appendix II',
                    'collection_restrictions' => 'Cutting or collection of wild Narra without DENR permit is illegal.'
                ],
                'sources' => [
                    ['name' => 'DENR Administrative Order DAO 2017-11', 'url' => 'https://bmb.gov.ph', 'type' => 'government'],
                    ['name' => 'IUCN Red List of Threatened Species - Pterocarpus indicus', 'url' => 'https://www.iucnredlist.org', 'type' => 'database']
                ]
            ],
            [
                'scientific_name' => 'Dillenia philippinensis',
                'primary_common_name' => 'Katmon',
                'kingdom' => 'Plantae',
                'family' => 'Dilleniaceae',
                'genus' => 'Dillenia',
                'species' => 'philippinensis',
                'native_status' => 'ENDEMIC',
                'habitat' => 'Primary and secondary forests at low and medium altitudes.',
                'philippine_distribution' => 'Endemic to the Philippines (Luzon, Polillo, Mindoro, Masbate, Leyte, Samar, Negros, Guimaras, Cebu, Mindanao).',
                'elevation_range' => '0 - 1000 m above sea level',
                'forest_type' => 'Lowland evergreen rainforest & submontane forest',
                'leaf_type' => 'Simple, thick, oblong to elliptic with coarse parallel side veins',
                'leaf_arrangement' => 'Alternate',
                'leaf_margin' => 'Coarsely serrate or spinulose-toothed',
                'leaf_apex' => 'Acute to obtuse',
                'leaf_base' => 'Rounded to cuneate',
                'venation' => 'Pinnate with very parallel prominent secondary veins terminating at teeth',
                'growth_habit' => 'Evergreen tree up to 15-17 meters tall',
                'bark_description' => 'Smooth, reddish-brown to dark reddish gray.',
                'flower_description' => 'Large white showy flowers (10-15cm wide) with red central carpels and stamens.',
                'fruit_description' => 'Globose edible fruit enclosed by fleshy persistent thick green sepals; sour citrus flavor.',
                'distinctive_markings' => 'Filipino endemic species featured on the 25-centimo coin; distinct sour edible segmented fruit.',
                'images' => [
                    ['file_path' => 'assets/images/species/katmon_fruit.jpg', 'filename' => 'katmon_fruit.jpg', 'type' => 'fruit']
                ],
                'names' => [
                    ['name' => 'Katmon', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Elephant Apple (Philippine)', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Palali', 'type' => 'local', 'region' => 'Ilocos/Cagayan'],
                    ['name' => 'Pamamien', 'type' => 'local', 'region' => 'Ibanag'],
                    ['name' => 'Kalingag-katmon', 'type' => 'regional', 'region' => 'Visayas']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Endemic Forest Canopy & Wildlife Food', 'description' => 'Fruits feed wild Philippine birds and mammals; ideal native tree for urban biodiversity.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Traditional Culinary Acidulant & Ornamental', 'description' => 'Sour fruit pulp used in traditional Sinigang broths, jams, and drinks.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Hair Tonic & Cough Syrup', 'description' => 'Fruit juice traditionally mixed with sugar to treat coughs and as a natural hair rinse.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Phytochemical Antioxidant Profile', 'description' => 'Fruit pulp contains high vitamin C, triterpenes, and betulinic acid with antioxidant properties.', 'evidence' => 'SUPPORTED_BY_SOME_RESEARCH']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'TRADITIONALLY_USED',
                    'traditional_uses_text' => 'Fruit juice used for coughs, fever, dysentery, and topical hair/scalp cleansing.',
                    'scientific_evidence_text' => 'Antioxidant and antimicrobial studies confirm phenolic compound presence in fruit extracts.',
                    'active_compounds' => 'Betulinic acid, dillentin, flavonoids, ascorbic acid.',
                    'known_risks' => 'High natural acidity may trigger acid reflux in sensitive stomachs.',
                    'known_interactions' => 'None documented.',
                    'toxic_parts' => 'None; fruit pulp is edible.',
                    'preparation_risks' => 'Wash outer sepals well before juicing.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'Ensure the fruit is fully developed before eating raw due to extreme sourness.',
                    'toxic_parts' => 'None.',
                    'look_alike_species' => 'Dillenia indica (Elephant Apple - Non-native)',
                    'look_alike_distinction' => 'Dillenia indica has much larger fruits (15cm+) and broader leaves with up to 30-40 vein pairs, native to South Asia.',
                    'warning_text' => 'PHILIPPINE ENDEMIC TREES SHOULD BE PRESERVED IN HABITAT.'
                ],
                'conservation' => [
                    'iucn_status' => 'Vulnerable (VU)',
                    'denr_status' => 'Other Threatened Species (DAO 2017-11)',
                    'threatened_status' => 'Threatened by habitat loss in lowland forests',
                    'protected_status' => 'Protected Native Species',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'Propagate via nursery seeds; avoid clearing wild stands.'
                ],
                'sources' => [
                    ['name' => 'Philippine Native Tree Enthusiasts (PNTE)', 'url' => 'https://pnte.org', 'type' => 'publication'],
                    ['name' => 'DENR DAO 2017-11 Threatened Flora List', 'url' => 'https://bmb.gov.ph', 'type' => 'government']
                ]
            ],
            [
                'scientific_name' => 'Senna alata',
                'primary_common_name' => 'Akapulko',
                'kingdom' => 'Plantae',
                'family' => 'Fabaceae',
                'genus' => 'Senna',
                'species' => 'alata',
                'native_status' => 'NATURALIZED',
                'habitat' => 'Open fields, village borders, waste ground, and river banks.',
                'philippine_distribution' => 'Widespread in all provinces of the Philippines.',
                'elevation_range' => '0 - 800 m above sea level',
                'forest_type' => 'Disturbed open secondary scrub and thickets',
                'leaf_type' => 'Pinnately compound with 8-14 pairs of large oblong leaflets',
                'leaf_arrangement' => 'Alternate',
                'leaf_margin' => 'Entire',
                'leaf_apex' => 'Rounded to emarginate',
                'leaf_base' => 'Oblique',
                'venation' => 'Pinnate',
                'growth_habit' => 'Coarse erect shrub 1-4 meters tall with thick green stems',
                'bark_description' => 'Smooth greenish-brown bark.',
                'flower_description' => 'Bright yellow erect candle-like spikes (terminal racemes).',
                'fruit_description' => 'Straight papery pod with prominent longitudinal wings containing flat triangular seeds.',
                'distinctive_markings' => 'Bright golden candle-shaped flower spikes; winged pods; crushed leaf antifungal remedy.',
                'images' => [
                    ['file_path' => 'assets/images/species/akapulko_flower.jpg', 'filename' => 'akapulko_flower.jpg', 'type' => 'flower']
                ],
                'names' => [
                    ['name' => 'Akapulko', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Ringworm Bush / Candle Bush', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Biyas-biyasan', 'type' => 'local', 'region' => 'Tagalog'],
                    ['name' => 'Kuntas', 'type' => 'local', 'region' => 'Ilocos'],
                    ['name' => 'Santing', 'type' => 'regional', 'region' => 'Visayas']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Pollinator & Soil Shrub', 'description' => 'Flowers attract native carpenter bees and butterflies.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Ornamental & Natural Antifungal', 'description' => 'Planted in herbal yards for antifungal leaf juice extraction.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Skin Fungal Poultice', 'description' => 'Fresh crushed leaves rubbed directly onto skin for tinea flava, ringworm, and scabies.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Antifungal Chrysophanic Acid Efficacy', 'description' => 'DOH-PITAHC officially approved herbal drug for skin fungal infections (tinea versicolor, eczema).', 'evidence' => 'ESTABLISHED']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'YES',
                    'traditional_uses_text' => 'Fresh crushed leaves applied topically for ringworm, athlete\'s foot, and skin rashes.',
                    'scientific_evidence_text' => 'DOH-PITAHC approved primary herbal medicine. Chrysophanic acid and anthraquinones demonstrate potent antifungal activity.',
                    'active_compounds' => 'Chrysophanic acid, emodin, aloe-emodin, rhein, kaempferol.',
                    'known_risks' => 'Mild local skin irritation in rare hypersensitive individuals.',
                    'known_interactions' => 'None documented for topical use.',
                    'toxic_parts' => 'Seeds contain anthraquinones that act as strong purgatives if ingested.',
                    'preparation_risks' => 'FOR EXTERNAL TOPICAL USE ONLY. Do not ingest fresh leaf decoctions in high doses.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'Apply extract topically. Avoid contact with eyes or open deep wounds.',
                    'toxic_parts' => 'Concentrated seed extracts.',
                    'look_alike_species' => 'Senna occidentalis (Balatong-aso)',
                    'look_alike_distinction' => 'Senna occidentalis has pointed leaf apices and unwinged cylindrical pods, lacking the erect dense candle-like flower spike.',
                    'warning_text' => 'TOPICAL APPLICATION ONLY FOR SKIN FUNGAL TREATMENT.'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Listed',
                    'threatened_status' => 'Abundant / Naturalized',
                    'protected_status' => 'Commonly Cultivated',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'Widespread in domestic herbal gardens.'
                ],
                'sources' => [
                    ['name' => 'DOH PITAHC 10 Medicinal Plants Pharmacopeia', 'url' => 'https://pitahc.gov.ph', 'type' => 'government'],
                    ['name' => 'Plants of the World Online (POWO - Senna alata)', 'url' => 'https://powo.science.kew.org', 'type' => 'database']
                ]
            ],
            [
                'scientific_name' => 'Carmona retusa',
                'primary_common_name' => 'Tsaang Gubat',
                'kingdom' => 'Plantae',
                'family' => 'Boraginaceae',
                'genus' => 'Carmona',
                'species' => 'retusa',
                'native_status' => 'NATIVE',
                'habitat' => 'Secondary forests, dry thickets, brushlands, and lowland plains.',
                'philippine_distribution' => 'Luzon, Mindoro, Masbate, Cebu, Negros, Bohol, Mindanao.',
                'elevation_range' => '0 - 800 m above sea level',
                'forest_type' => 'Dry secondary scrub and forest margins',
                'leaf_type' => 'Simple, small (1-5cm), obovate, clustered in fascicles',
                'leaf_arrangement' => 'Alternate or fascicled',
                'leaf_margin' => 'Coarsely 3-5 toothed towards the apex',
                'leaf_apex' => 'Obtuse to 3-lobed',
                'leaf_base' => 'Cuneate',
                'venation' => 'Pinnate, leaves rough with scabrous short white bristles',
                'growth_habit' => 'Erect densely branched shrub 1-4 meters tall',
                'bark_description' => 'Grayish-brown, slender twiggy branching.',
                'flower_description' => 'Small white 5-lobed star-shaped flowers in axillary cymes.',
                'fruit_description' => 'Small globose fleshy red or yellow drupe containing a 4-seeded nutlet.',
                'distinctive_markings' => 'Small thick dark green leaves with 3-5 teeth at tip and rough sandpaper-like white hairy texture; small white star flowers.',
                'images' => [
                    ['file_path' => 'assets/images/species/tsaang_gubat_leaf.jpg', 'filename' => 'tsaang_gubat_leaf.jpg', 'type' => 'leaf']
                ],
                'names' => [
                    ['name' => 'Tsaang Gubat', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Wild Tea / Fukien Tea Tree', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Alangitngit', 'type' => 'tagalog', 'region' => 'Southern Tagalog'],
                    ['name' => 'Gitingan', 'type' => 'local', 'region' => 'Ilocos'],
                    ['name' => 'Kalimugong', 'type' => 'regional', 'region' => 'Visayas']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Scrubland Habitat & Bird Food', 'description' => 'Fleshy drupe fruits provide food for wild native birds.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Bonsai & Living Hedge', 'description' => 'Popular ornamental bonsai shrub and traditional living perimeter hedge.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Postpartum & Diarrhea Tea', 'description' => 'Leaf decoction consumed as a traditional tea for stomach pain, diarrhea, and mouth rinse.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Spasmolytic & Anti-allergy Tea', 'description' => 'DOH-PITAHC approved primary medicine for colic stomach pain, diarrhea, and oral gargle.', 'evidence' => 'ESTABLISHED']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'YES',
                    'traditional_uses_text' => 'Boiled leaves taken for stomach ache, abdominal cramps, diarrhea, and used as a fluoride mouth gargle.',
                    'scientific_evidence_text' => 'DOH-PITAHC approved primary herbal drug. Demonstrates spasmolytic action on smooth intestinal muscle and antibacterial activity against dental plaque.',
                    'active_compounds' => 'Rosmarinic acid, microphyllone, triterpenes (α- and β-amyrin), nitidine.',
                    'known_risks' => 'None documented under standard decoction dosage.',
                    'known_interactions' => 'No adverse drug interactions reported.',
                    'toxic_parts' => 'None.',
                    'preparation_risks' => 'Wash leaves before boiling.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'Severe persistent bloody dysentery requires clinical medical evaluation.',
                    'toxic_parts' => 'None.',
                    'look_alike_species' => 'Premna odorata (Alagaw)',
                    'look_alike_distinction' => 'Premna odorata has much larger leaves (8-20cm) with a aromatic aromatic scent, unlike the tiny sandpaper-textured leaves of Tsaang Gubat.',
                    'warning_text' => 'VERIFY LEAF SIZE (TSAANG GUBAT LEAVES ARE SMALL 1-4 CM).'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Listed',
                    'threatened_status' => 'Safe',
                    'protected_status' => 'Commonly Cultivated',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'Propagated readily via stem cuttings.'
                ],
                'sources' => [
                    ['name' => 'DOH PITAHC Approved Medicinal Plants', 'url' => 'https://pitahc.gov.ph', 'type' => 'government'],
                    ['name' => 'NIRPROMP Pharmacological Studies', 'url' => 'https://upm.edu.ph', 'type' => 'academic']
                ]
            ],
            [
                'scientific_name' => 'Jatropha curcas',
                'primary_common_name' => 'Tubang Bakod',
                'kingdom' => 'Plantae',
                'family' => 'Euphorbiaceae',
                'genus' => 'Jatropha',
                'species' => 'curcas',
                'native_status' => 'INTRODUCED',
                'habitat' => 'Hedges, fence lines, roadsides, waste ground, dry open scrub.',
                'philippine_distribution' => 'Introduced during Spanish colonial era; naturalized in all Philippine islands.',
                'elevation_range' => '0 - 800 m above sea level',
                'forest_type' => 'Disturbed scrub and rural fence margins',
                'leaf_type' => 'Simple, 3-5 shallowly lobed, heart-shaped base',
                'leaf_arrangement' => 'Alternate',
                'leaf_margin' => 'Entire or wavy',
                'leaf_apex' => 'Acuminate',
                'leaf_base' => 'Cordate (heart-shaped)',
                'venation' => 'Palmate (5-7 primary veins)',
                'growth_habit' => 'Coarse, thick-stemmed shrub or small tree 3-6 meters tall with copious clear/milky sap',
                'bark_description' => 'Smooth yellowish-green papery peeling bark.',
                'flower_description' => 'Small yellowish-green monoecious flowers in axillary cymes.',
                'fruit_description' => 'Capsule containing 3 black oil-rich seeds.',
                'distinctive_markings' => 'HIGHLY POISONOUS SEEDS; abundant sticky sap when stem or petiole is broken; cordate lobed leaves.',
                'images' => [
                    ['file_path' => 'assets/images/species/poison_jatropha.jpg', 'filename' => 'poison_jatropha.jpg', 'type' => 'leaf']
                ],
                'names' => [
                    ['name' => 'Tubang Bakod', 'type' => 'tagalog', 'region' => 'Luzon'],
                    ['name' => 'Physic Nut', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Tawatawa (Ilocano)', 'type' => 'local', 'region' => 'Ilocos (Note: Not Euphorbia hirta)'],
                    ['name' => 'Kasla', 'type' => 'regional', 'region' => 'Visayas'],
                    ['name' => 'Tuba-tuba', 'type' => 'regional', 'region' => 'Cebu/Bicol']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Living Fence & Erosion Barrier', 'description' => 'Planted along boundary fences because livestock avoid eating it due to toxicity.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Biofuel Oil & Organic Insecticide', 'description' => 'Seed oil harvested for biodiesel production and saponified pest sprays.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Topical Sap Poultice', 'description' => 'Bark sap traditionally applied topically to stop minor bleeding and skin fungal spots.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Phorbol Ester & Toxalbumin Research', 'description' => 'Extensive research highlights high toxicity of curcin toxalbumin and phorbol esters in seeds.', 'evidence' => 'ESTABLISHED']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'NO_RELIABLE_USE',
                    'traditional_uses_text' => 'Topical stem latex used traditionally for minor cuts; seed decoction historically used as a severe purgative (now strongly discouraged due to poison risk).',
                    'scientific_evidence_text' => 'INTERNAL INGESTION IS DANGEROUS AND TOXIC. Contains curcin, a ribosome-inactivating protein similar to ricin.',
                    'active_compounds' => 'Curcin (toxalbumin), phorbol esters, curcanoleic acid.',
                    'known_risks' => 'Ingestion of seeds causes severe gastroenteritis, vomiting, diarrhea, dehydration, organ damage, and potential death.',
                    'known_interactions' => 'Highly toxic; no medicinal internal co-administration.',
                    'toxic_parts' => 'SEEDS, LEAVES, SAP, AND ROOT ARE ALL POISONOUS.',
                    'preparation_risks' => 'DO NOT INGEST ANY PART OF THIS PLANT.'
                ],
                'safety' => [
                    'safety_category' => 'KNOWN_POISONOUS_PLANT',
                    'primary_warning' => 'DANGER: SEEDS ARE HIGHLY POISONOUS AND CAN BE FATAL IF INGESTED BY CHILDREN OR ADULTS. DO NOT CONSUME.',
                    'toxic_parts' => 'Seeds contain deadly toxalbumins (curcin) and phorbol esters.',
                    'look_alike_species' => 'Ricinus communis (Tangan-tangan / Castor Bean)',
                    'look_alike_distinction' => 'Ricinus communis has deeply star-pinnatifid (7-11 deeply cut lobes) palmately parted leaves and spiky fruits, whereas Jatropha curcas has shallowly 3-5 lobed smooth fruits.',
                    'warning_text' => 'DO NOT CONSUME UNTIL THE IDENTIFICATION HAS BEEN VERIFIED. HIGH TOXICITY WARNING.'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Listed (Introduced Species)',
                    'threatened_status' => 'Abundant Weed / Cultivated Fence',
                    'protected_status' => 'Unprotected',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'Widespread alien naturalized plant.'
                ],
                'sources' => [
                    ['name' => 'Toxic Plants of the Philippines Reference Manual', 'url' => 'https://doh.gov.ph', 'type' => 'government'],
                    ['name' => 'World Health Organization Poison Information Monograph - Jatropha curcas', 'url' => 'https://who.int', 'type' => 'publication']
                ]
            ],
            [
                'scientific_name' => 'Diospyros blancoí',
                'primary_common_name' => 'Kamagong',
                'kingdom' => 'Plantae',
                'family' => 'Ebenaceae',
                'genus' => 'Diospyros',
                'species' => 'blancoí',
                'native_status' => 'NATIVE',
                'habitat' => 'Primary and secondary lowland forests at low altitudes.',
                'philippine_distribution' => 'Luzon, Mindoro, Palawan, Leyte, Samar, Panay, Mindanao.',
                'elevation_range' => '0 - 800 m above sea level',
                'forest_type' => 'Lowland evergreen rainforest',
                'leaf_type' => 'Simple, thick, oblong, dark glossy green upper side and velvety silky hairy golden underside',
                'leaf_arrangement' => 'Alternate',
                'leaf_margin' => 'Entire',
                'leaf_apex' => 'Acute to acuminate',
                'leaf_base' => 'Rounded to attenuate',
                'venation' => 'Pinnate',
                'growth_habit' => 'Large hardwood evergreen tree 20-33 meters tall with dense dark crown',
                'bark_description' => 'Dark brown to jet black, deeply fissured outer bark.',
                'flower_description' => 'Creamy-white 4-petaled fragrant flowers.',
                'fruit_description' => 'Globose fruit (Mabolo) covered in dense reddish-brown velvet hairs containing sweet reddish-white aromatic flesh.',
                'distinctive_markings' => 'Jet-black iron-wood heartwood (Ironwood); velvet-hairy edible Mabolo fruit; golden silky leaf underside.',
                'images' => [
                    ['file_path' => 'assets/images/species/kamagong_tree.jpg', 'filename' => 'kamagong_tree.jpg', 'type' => 'whole_plant']
                ],
                'names' => [
                    ['name' => 'Kamagong', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Mabolo / Velvet Apple', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Ironwood (Philippine)', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Amaga', 'type' => 'local', 'region' => 'Bicol'],
                    ['name' => 'Talang', 'type' => 'regional', 'region' => 'Pampanga']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Keystone Forest Canopy & Frugivore Food', 'description' => 'Velvet apple fruits feed Philippine fruit bats, birds, and civets.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Precious Hardwood & Edible Fruit', 'description' => 'Famous black heartwood used for traditional carving, arnis sticks, and fine art; fruits eaten fresh.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Bark Infusion for Diarrhea', 'description' => 'Bark decoction traditionally used as an astringent gargle and for intestinal cramps.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Antimicrobial Triterpenoids', 'description' => 'Lupeol, betulin, and amyrin compounds isolated from bark exhibit antibacterial activity.', 'evidence' => 'SUPPORTED_BY_SOME_RESEARCH']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'TRADITIONALLY_USED',
                    'traditional_uses_text' => 'Bark and unripe fruit decoctions used traditionally for diarrhea, dysentery, and skin washing.',
                    'scientific_evidence_text' => 'Phytochemical screening reveals high tannins, lupeol, and betulinic acid with anti-inflammatory properties.',
                    'active_compounds' => 'Lupeol, betulin, β-sitosterol, plumbagin, tannins.',
                    'known_risks' => 'Unripe fruits contain high tannin concentrations causing astringent oral irritation.',
                    'known_interactions' => 'None reported.',
                    'toxic_parts' => 'Hairs on outer skin of fruit can irritate skin and throat if not rubbed off before eating.',
                    'preparation_risks' => 'Peel and rub off velvety surface hairs before consuming fruit.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'PROTECTED NATIVE TREE: Wild harvesting of Kamagong timber is strictly regulated under DENR forestry conservation laws.',
                    'toxic_parts' => 'Irritating velvet hairs on fruit outer skin.',
                    'look_alike_species' => 'Diospyros ebenum (Ceylon Ebony)',
                    'look_alike_distinction' => 'Diospyros ebenum lacks the distinctive reddish velvet hairy fruit (Mabolo) and silvery-golden leaf underside of D. blancoí.',
                    'warning_text' => 'PROTECTED SPECIES. RUB OFF FRUIT HAIRS BEFORE EATING MABOLO.'
                ],
                'conservation' => [
                    'iucn_status' => 'Vulnerable (VU)',
                    'denr_status' => 'Threatened Species (DAO 2017-11)',
                    'threatened_status' => 'Threatened by historic over-logging for precious timber',
                    'protected_status' => 'Protected Native Hardwood',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'Cutting wild trees requires DENR permits.'
                ],
                'sources' => [
                    ['name' => 'DENR Administrative Order DAO 2017-11', 'url' => 'https://bmb.gov.ph', 'type' => 'government'],
                    ['name' => 'IUCN Red List - Diospyros blancoí', 'url' => 'https://www.iucnredlist.org', 'type' => 'database']
                ]
            ],
            [
                'scientific_name' => 'Vitex parviflora',
                'primary_common_name' => 'Molave',
                'kingdom' => 'Plantae',
                'family' => 'Lamiaceae',
                'genus' => 'Vitex',
                'species' => 'parviflora',
                'native_status' => 'NATIVE',
                'habitat' => 'Lowland limestone forests, dry coastal hills, and open secondary woods.',
                'philippine_distribution' => 'Widespread across Luzon, Mindoro, Cebu, Bohol, Leyte, Samar, Mindanao.',
                'elevation_range' => '0 - 700 m above sea level',
                'forest_type' => 'Molave forest on limestone (Karst formations)',
                'leaf_type' => 'Trifoliate compound leaf with 3 lanceolate glossy leaflets',
                'leaf_arrangement' => 'Opposite',
                'leaf_margin' => 'Entire',
                'leaf_apex' => 'Acuminate',
                'leaf_base' => 'Cuneate',
                'venation' => 'Pinnate',
                'growth_habit' => 'Medium to large tree 15-30 meters tall with crooked fluted trunk and yellowish wood',
                'bark_description' => 'Light gray to yellowish-brown, smooth or shedding in thin scales.',
                'flower_description' => 'Blue to pale violet small flowers in terminal panicles.',
                'fruit_description' => 'Small globose succulent purplish-black drupe.',
                'distinctive_markings' => 'Trifoliate leaves (strictly 3 leaflets); yellowish durable wood turning olive green when submerged in water.',
                'images' => [
                    ['file_path' => 'assets/images/species/molave_tree.jpg', 'filename' => 'molave_tree.jpg', 'type' => 'whole_plant']
                ],
                'names' => [
                    ['name' => 'Molave', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Small-flower Chaste Tree', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Tugas', 'type' => 'local', 'region' => 'Visayas'],
                    ['name' => 'Sagad', 'type' => 'local', 'region' => 'Ilocos'],
                    ['name' => 'Hamurawon', 'type' => 'regional', 'region' => 'Bicol/Samar']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Limestone Karst Indicator & Wildlife Habitat', 'description' => 'Key canopy tree species in native limestone forests, preventing soil erosion on karst hills.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Ultra-Durable Timber & Watershed Tree', 'description' => 'Historic construction timber for railroad ties, bridges, and ship framing; vital native reforestation tree.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Wood Shavings Tea for Wounds', 'description' => 'Wood bark decoction traditionally applied for wash on wounds and dropsy.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Limonoid & Flavonoid Content', 'description' => 'Phytochemical analysis confirms presence of vitexin and iridoid glycosides.', 'evidence' => 'PRELIMINARY_EVIDENCE']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'TRADITIONALLY_USED',
                    'traditional_uses_text' => 'Wood shavings or bark decoction used traditionally for cleansing wounds and treating poison bites.',
                    'scientific_evidence_text' => 'Contains iridoids and vitexin flavonoids with mild antioxidant and anti-inflammatory activity.',
                    'active_compounds' => 'Vitexin, casticin, iridoid glycosides.',
                    'known_risks' => 'None documented.',
                    'known_interactions' => 'None reported.',
                    'toxic_parts' => 'None.',
                    'preparation_risks' => 'Do not damage wild living trunks.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'PROTECTED SPECIES: Wild Molave trees in limestone forests are protected under Philippine environmental laws.',
                    'toxic_parts' => 'None.',
                    'look_alike_species' => 'Vitex negundo (Lagundi)',
                    'look_alike_distinction' => 'Lagundi has 5 leaflets (palmate) and smaller shrub habit, whereas Molave has strictly 3 leaflets (trifoliate) and grows as a large canopy tree.',
                    'warning_text' => 'COUNT LEAFLETS (MOLAVE HAS 3 LEAFLETS, LAGUNDI HAS 5).'
                ],
                'conservation' => [
                    'iucn_status' => 'Vulnerable (VU)',
                    'denr_status' => 'Endangered / Protected Tree',
                    'threatened_status' => 'Threatened by historical over-logging',
                    'protected_status' => 'Strictly Protected Forestry Species',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'Cutting wild trees prohibited without DENR permit.'
                ],
                'sources' => [
                    ['name' => 'DENR Biodiversity Management Bureau Protected Flora', 'url' => 'https://bmb.gov.ph', 'type' => 'government'],
                    ['name' => 'Philippine Native Tree Enthusiasts (PNTE Guide)', 'url' => 'https://pnte.org', 'type' => 'publication']
                ]
            ],
            [
                'scientific_name' => 'Psidium guajava',
                'primary_common_name' => 'Bayabas',
                'kingdom' => 'Plantae',
                'family' => 'Myrtaceae',
                'genus' => 'Psidium',
                'species' => 'guajava',
                'native_status' => 'NATURALIZED',
                'habitat' => 'Backyards, secondary thickets, agricultural lands, and open fields.',
                'philippine_distribution' => 'Abundant across all islands and provinces of the Philippines.',
                'elevation_range' => '0 - 1500 m above sea level',
                'forest_type' => 'Secondary thickets and agricultural landscapes',
                'leaf_type' => 'Simple, thick, elliptic to oblong, prominent parallel veins',
                'leaf_arrangement' => 'Opposite',
                'leaf_margin' => 'Entire',
                'leaf_apex' => 'Acute to obtuse',
                'leaf_base' => 'Rounded',
                'venation' => 'Pinnate with strongly sunken veins above and prominent below',
                'growth_habit' => 'Small tree or large shrub 3-8 meters tall',
                'bark_description' => 'Smooth, copper-colored or light brown, peeling in thin sheets.',
                'flower_description' => 'White fragrant flowers with numerous prominent white stamens.',
                'fruit_description' => 'Globose or pear-shaped berry, yellow when ripe, sweet edible pink or white pulp.',
                'distinctive_markings' => 'Aromatic guava smell; smooth copper peeling bark; opposite leaves with sunken veins; DOH approved antiseptic.',
                'images' => [
                    ['file_path' => 'assets/images/species/bayabas_leaf.jpg', 'filename' => 'bayabas_leaf.jpg', 'type' => 'leaf'],
                    ['file_path' => 'assets/images/species/bayabas_fruit.jpg', 'filename' => 'bayabas_fruit.jpg', 'type' => 'fruit']
                ],
                'names' => [
                    ['name' => 'Bayabas', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Guava', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Geyabas', 'type' => 'local', 'region' => 'Ilocos'],
                    ['name' => 'Tayabas', 'type' => 'local', 'region' => 'Cagayan'],
                    ['name' => 'Biabas', 'type' => 'regional', 'region' => 'Mindanao/Visayas']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Wildlife Food & Shade', 'description' => 'Fruits feed native birds, bats, and small mammals.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Fruit Crop & Fuelwood', 'description' => 'Cultivated in home gardens for nutrient-rich fruit (high Vitamin C).', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Antiseptic Wash & Toothache Relief', 'description' => 'Leaf decoction widely used for washing wounds, circumcision care, and gargle for toothache.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Antimicrobial & Astringent Activity', 'description' => 'DOH-PITAHC approved primary herbal drug for wound disinfection and mouth wash.', 'evidence' => 'ESTABLISHED']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'YES',
                    'traditional_uses_text' => 'Warm leaf decoction used for cleansing wounds, skin ulcers, diarrhea, and mouth sores.',
                    'scientific_evidence_text' => 'DOH-PITAHC approved 10 priority medicinal plant. Rich in tannins and flavonoids with antibacterial action against Staphylococcus aureus and E. coli.',
                    'active_compounds' => 'Guaijaverin, quercetin, tannins, β-sitosterol, essential oils.',
                    'known_risks' => 'Constipation if excessive decoction is ingested internally due to astringent tannins.',
                    'known_interactions' => 'None reported for topical antiseptic wash.',
                    'toxic_parts' => 'None.',
                    'preparation_risks' => 'Ensure decoction is cooled to warm temperature before wound washing.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'Deep infected surgical wounds require professional sterile medical dressing.',
                    'toxic_parts' => 'None.',
                    'look_alike_species' => 'Psidium littorale (Strawberry Guava)',
                    'look_alike_distinction' => 'Psidium littorale has smaller glossy dark red/yellow fruits and smooth glossy non-sunken leaves.',
                    'warning_text' => 'WASH LEAF DECOCTION WARM, NOT SCALDING HOT.'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Listed',
                    'threatened_status' => 'Abundant',
                    'protected_status' => 'Commonly Cultivated',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'None.'
                ],
                'sources' => [
                    ['name' => 'DOH PITAHC Approved 10 Medicinal Plants', 'url' => 'https://pitahc.gov.ph', 'type' => 'government']
                ]
            ],
            [
                'scientific_name' => 'Peperomia pellucida',
                'primary_common_name' => 'Pansit-pansitan',
                'kingdom' => 'Plantae',
                'family' => 'Piperaceae',
                'genus' => 'Peperomia',
                'species' => 'pellucida',
                'native_status' => 'NATURALIZED',
                'habitat' => 'Damp shaded areas, plant pots, rock crevices, brick walls, riverbanks.',
                'philippine_distribution' => 'Abundant throughout the Philippines.',
                'elevation_range' => '0 - 1000 m above sea level',
                'forest_type' => 'Damp shaded understory, gardens, urban damp walls',
                'leaf_type' => 'Simple, translucent, fleshy heart-shaped (cordate) leaves',
                'leaf_arrangement' => 'Alternate',
                'leaf_margin' => 'Entire',
                'leaf_apex' => 'Acute',
                'leaf_base' => 'Cordate',
                'venation' => 'Palmate (3-5 faint veins)',
                'growth_habit' => 'Small succulent annual herb 10-40 cm tall with translucent watery stems',
                'bark_description' => 'Herbaceous, delicate clear light green stem.',
                'flower_description' => 'Tiny green spike flowers protruding from leaf axils.',
                'fruit_description' => 'Tiny globose seed attached to flower spike.',
                'distinctive_markings' => 'Translucent succulent heart-shaped leaf; watery stem; edible mild mustard/cucumber taste; DOH approved for gout.',
                'images' => [
                    ['file_path' => 'assets/images/species/pansit_pansitan_plant.jpg', 'filename' => 'pansit_pansitan_plant.jpg', 'type' => 'whole_plant'],
                    ['file_path' => 'assets/images/species/pansit_pansitan_leaf.jpg', 'filename' => 'pansit_pansitan_leaf.jpg', 'type' => 'leaf']
                ],
                'names' => [
                    ['name' => 'Pansit-pansitan', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Ulasimang Bato', 'type' => 'tagalog', 'region' => 'Tagalog'],
                    ['name' => 'Clearweed / Shiny Bush', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Sinaw-sinaw', 'type' => 'regional', 'region' => 'Visayas'],
                    ['name' => 'Tangon-tangon', 'type' => 'regional', 'region' => 'Bicol']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Shade Ground Cover', 'description' => 'Covers damp soil, preventing topsoil erosion in garden shaded beds.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Edible Salad Herb', 'description' => 'Leaves and stems eaten fresh in raw salads.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Gout & Arthritis Salad', 'description' => 'Fresh leaves consumed raw or boiled as a tea for reducing joint swelling and uric acid.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Hyperuricemia & Anti-gout Efficacy', 'description' => 'DOH-PITAHC approved 10 priority herbal drug for lowering blood uric acid levels.', 'evidence' => 'ESTABLISHED']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'YES',
                    'traditional_uses_text' => 'Eaten fresh or as warm tea for gout, arthritis, kidney conditions, and facial acne wash.',
                    'scientific_evidence_text' => 'DOH-PITAHC approved primary herbal drug. Xanthine oxidase inhibitory activity confirmed in pharmacological trials, lowering serum uric acid.',
                    'active_compounds' => 'Pellucidatin, apiol, phytols, acacetin, flavonoids.',
                    'known_risks' => 'Mild asthmatic or allergic skin reaction in rare individuals.',
                    'known_interactions' => 'Synergistic with synthetic uric acid lowering drugs like Allopurinol.',
                    'toxic_parts' => 'None; whole herb is edible.',
                    'preparation_risks' => 'Wash thoroughly with clean water if harvested near urban ground runoff.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'Do not harvest from roadside soils exposed to vehicle heavy metal runoff or pet contamination.',
                    'toxic_parts' => 'None.',
                    'look_alike_species' => 'Pilea microphylla (Angel\'s Tears / Gunpowder Plant)',
                    'look_alike_distinction' => 'Pilea microphylla has tiny fern-like leaves (2-4mm) arranged densely along stems, unlike the clear heart-shaped leaves of Pansit-pansitan.',
                    'warning_text' => 'HARVEST FROM CLEAN GARDEN BEDS ONLY.'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Listed',
                    'threatened_status' => 'Abundant',
                    'protected_status' => 'Unprotected Groundweed',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'None.'
                ],
                'sources' => [
                    ['name' => 'DOH PITAHC Approved 10 Medicinal Plants', 'url' => 'https://pitahc.gov.ph', 'type' => 'government']
                ]
            ],
            [
                'scientific_name' => 'Moringa oleifera',
                'primary_common_name' => 'Malunggay',
                'kingdom' => 'Plantae',
                'family' => 'Moringaceae',
                'genus' => 'Moringa',
                'species' => 'oleifera',
                'native_status' => 'NATURALIZED',
                'habitat' => 'Backyards, farms, roadsides, tropical lowlands everywhere.',
                'philippine_distribution' => 'Cultivated and naturalized in all 82 Philippine provinces.',
                'elevation_range' => '0 - 1200 m above sea level',
                'forest_type' => 'Agricultural, urban backyard, and tropical lowlands',
                'leaf_type' => 'Tripinnately compound with small rounded elliptic leaflets',
                'leaf_arrangement' => 'Alternate',
                'leaf_margin' => 'Entire',
                'leaf_apex' => 'Obtuse',
                'leaf_base' => 'Cuneate',
                'venation' => 'Pinnate on leaflets',
                'growth_habit' => 'Small fast-growing softwood tree 5-10 meters tall',
                'bark_description' => 'Corki, whitish-gray, soft wood.',
                'flower_description' => 'Creamy white yellowish fragrant flowers in drooping panicles.',
                'fruit_description' => 'Long 3-angled ribbed pendulous pod (drumstick) containing 3-winged seeds.',
                'distinctive_markings' => 'Tripinnate delicate leaves; long ribbed drumstick pods; superfood vegetable rich in iron and calcium.',
                'images' => [
                    ['file_path' => 'assets/images/species/malunggay_leaf.jpg', 'filename' => 'malunggay_leaf.jpg', 'type' => 'leaf'],
                    ['file_path' => 'assets/images/species/malunggay_pod.jpg', 'filename' => 'malunggay_pod.jpg', 'type' => 'fruit']
                ],
                'names' => [
                    ['name' => 'Malunggay', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Moringa / Drumstick Tree', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Marunggay', 'type' => 'local', 'region' => 'Ilocos'],
                    ['name' => 'Kamalunggay', 'type' => 'regional', 'region' => 'Visayas'],
                    ['name' => 'Kalungai', 'type' => 'regional', 'region' => 'Bicol']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Soil Improvement & Agroforestry', 'description' => 'Leaves drop and enrich soil nitrogen; fast biomass producer.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Superfood Vegetable & Fodder', 'description' => 'National superfood vegetable in Tinola soups; high protein livestock feed.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Lactation Enhancer (Galactagogue)', 'description' => 'Boiled leaves given to nursing mothers to stimulate breastmilk production.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Galactagogue & Nutritional Profile', 'description' => 'Rich in Vitamin A, C, Calcium, Potassium, and Iron; clinically supported galactagogue.', 'evidence' => 'ESTABLISHED']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'YES',
                    'traditional_uses_text' => 'Leaves consumed for lactation enhancement, anemia prevention, immunity booster, and blood pressure management.',
                    'scientific_evidence_text' => 'High concentration of antioxidant polyphenols, glucosinolates, and essential minerals. Endorsed by National Nutrition Council.',
                    'active_compounds' => 'Moringine, quercetin, chlorogenic acid, β-carotene, iron, calcium.',
                    'known_risks' => 'High doses of root or bark extracts contain alkaloids that can cause uterine contractions (avoid root/bark during pregnancy).',
                    'known_interactions' => 'May enhance blood pressure and thyroid medication effects.',
                    'toxic_parts' => 'Root and bark extracts in concentrated doses (contain moringinine). Leaves are completely safe.',
                    'preparation_risks' => 'Eat leaves and young pods; avoid consuming raw thick root bark during pregnancy.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'LEAVES ARE COMPLETELY SAFE SUPERFOOD. Pregnant women should avoid root/bark concentrated extracts.',
                    'toxic_parts' => 'Root bark in excessive concentrated doses.',
                    'look_alike_species' => 'Leucaena leucocephala (Ipil-ipil)',
                    'look_alike_distinction' => 'Ipil-ipil leaves are bipinnate with flat brown seed pods, whereas Malunggay leaves are tripinnate with long 3-angled drumstick pods.',
                    'warning_text' => 'SAFE NUTRITIONAL SUPERFOOD LEAVES.'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Listed',
                    'threatened_status' => 'Abundant',
                    'protected_status' => 'Widely Cultivated',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'None.'
                ],
                'sources' => [
                    ['name' => 'National Nutrition Council (NNC) Philippines', 'url' => 'https://nnc.gov.ph', 'type' => 'government'],
                    ['name' => 'Food and Agriculture Organization (FAO) Moringa Monograph', 'url' => 'https://fao.org', 'type' => 'publication']
                ]
            ],
            [
                'scientific_name' => 'Andrographis paniculata',
                'primary_common_name' => 'Serpentina',
                'kingdom' => 'Plantae',
                'family' => 'Acanthaceae',
                'genus' => 'Andrographis',
                'species' => 'paniculata',
                'native_status' => 'INTRODUCED',
                'habitat' => 'Herbal gardens, cultivated beds, waste ground.',
                'philippine_distribution' => 'Cultivated across Philippine gardens and provinces.',
                'elevation_range' => '0 - 800 m above sea level',
                'forest_type' => 'Garden herb beds and secondary shaded clearings',
                'leaf_type' => 'Simple, lanceolate, dark green, smooth',
                'leaf_arrangement' => 'Opposite',
                'leaf_margin' => 'Entire',
                'leaf_apex' => 'Acuminate',
                'leaf_base' => 'Cuneate',
                'venation' => 'Pinnate',
                'growth_habit' => 'Erect annual herb 30-90 cm tall with square dark green stems',
                'bark_description' => 'Herbaceous, quadrangular (4-angled) dark green stem.',
                'flower_description' => 'Small tubular white flowers with purple streaks in axillary racemes.',
                'fruit_description' => 'Erect linear-oblong capsule containing yellow-brown seeds.',
                'distinctive_markings' => 'EXTREMELY BITTER TASTE ("King of Bitters"); 4-angled square green stem; dark green opposite leaves.',
                'images' => [
                    ['file_path' => 'assets/images/species/serpentina_plant.jpg', 'filename' => 'serpentina_plant.jpg', 'type' => 'whole_plant'],
                    ['file_path' => 'assets/images/species/serpentina_leaf.jpg', 'filename' => 'serpentina_leaf.jpg', 'type' => 'leaf']
                ],
                'names' => [
                    ['name' => 'Serpentina', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'King of Bitters / Creat', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Sinta', 'type' => 'local', 'region' => 'Tagalog'],
                    ['name' => 'Alpasotes-bitter', 'type' => 'regional', 'region' => 'Visayas']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Garden Pollinator Herb', 'description' => 'Small tubular flowers visited by tiny wild solitary bees.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Herbal Garden Staple', 'description' => 'Widely grown in Philippine community herbal gardens.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Diabetes & Fever Decoction', 'description' => 'Leaf tea consumed for bitter blood glucose lowering, colds, and fever.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Andrographolide Immunomodulatory Research', 'description' => 'Contains andrographolides studied extensively for immunostimulant and anti-inflammatory properties.', 'evidence' => 'SUPPORTED_BY_SOME_RESEARCH']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'POTENTIAL',
                    'traditional_uses_text' => 'Tradition dictates drinking leaf tea for diabetes management, hypertension, and upper respiratory fever.',
                    'scientific_evidence_text' => 'Andrographolide diterpenes demonstrate in-vitro immunostimulatory and antiviral activity. Requires physician guidance for diabetic patients.',
                    'active_compounds' => 'Andrographolide, neoandrographolide, 14-deoxyandrographolide, flavonoids.',
                    'known_risks' => 'High doses may cause gastric discomfort, loss of taste, or allergic skin hives.',
                    'known_interactions' => 'May interact with blood pressure, antidiabetic, and anticoagulant medications.',
                    'toxic_parts' => 'None at normal herbal doses.',
                    'preparation_risks' => 'DO NOT CONSUME DURING PREGNANCY (possesses abortifacient potential in high animal doses).'
                ],
                'safety' => [
                    'safety_category' => 'CAUTION',
                    'primary_warning' => 'CONTRAINDICATED IN PREGNANCY. Diabetic patients must monitor blood sugar to avoid hypoglycemia.',
                    'toxic_parts' => 'High concentrated doses.',
                    'look_alike_species' => 'Rauvolfia serpentina (Indian Snakeroot - Toxic)',
                    'look_alike_distinction' => 'Rauvolfia serpentina has whorled leaves (3-4 at node) and fleshy red/black berries containing powerful reserpine alkaloids, unlike Andrographis paniculata which has opposite leaves and square stems.',
                    'warning_text' => 'VERIFY SQUARE STEM AND OPPOSITE LEAF ARRANGEMENT.'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Listed',
                    'threatened_status' => 'Safe',
                    'protected_status' => 'Cultivated Herb',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'Cultivated in home herbal plots.'
                ],
                'sources' => [
                    ['name' => 'Plants of the World Online (POWO - Andrographis paniculata)', 'url' => 'https://powo.science.kew.org', 'type' => 'database'],
                    ['name' => 'WHO Monographs on Selected Medicinal Plants - Vol 2', 'url' => 'https://who.int', 'type' => 'publication']
                ]
            ],
            [
                'scientific_name' => 'Curcuma longa',
                'primary_common_name' => 'Luyang Dilaw',
                'kingdom' => 'Plantae',
                'family' => 'Zingiberaceae',
                'genus' => 'Curcuma',
                'species' => 'longa',
                'native_status' => 'NATURALIZED',
                'habitat' => 'Farms, home gardens, damp shaded tropical soils.',
                'philippine_distribution' => 'Cultivated extensively throughout the Philippines.',
                'elevation_range' => '0 - 1200 m above sea level',
                'forest_type' => 'Agricultural crops and forest understory garden plots',
                'leaf_type' => 'Simple, large (30-90cm), oblong-lanceolate, smooth green',
                'leaf_arrangement' => 'Basal tufted tufts',
                'leaf_margin' => 'Entire',
                'leaf_apex' => 'Acuminate',
                'leaf_base' => 'Attenuate',
                'venation' => 'Parallel from central midrib',
                'growth_habit' => 'Perennial rhizomatous herb 60-100 cm tall with deep bright orange rhizome',
                'bark_description' => 'Herbaceous pseudostem formed by leaf sheaths.',
                'flower_description' => 'Pale yellow spike enclosed in whitish-green to pinkish bracts.',
                'fruit_description' => 'Rarely fruits; propagated by rhizome division.',
                'distinctive_markings' => 'Deep orange-yellow interior rhizome; aromatic yellow dye stain; broad smooth basal leaves.',
                'images' => [
                    ['file_path' => 'assets/images/species/luyang_dilaw_rhizome.jpg', 'filename' => 'luyang_dilaw_rhizome.jpg', 'type' => 'whole_plant'],
                    ['file_path' => 'assets/images/species/luyang_dilaw_leaf.jpg', 'filename' => 'luyang_dilaw_leaf.jpg', 'type' => 'leaf']
                ],
                'names' => [
                    ['name' => 'Luyang Dilaw', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Turmeric', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Dulaw', 'type' => 'regional', 'region' => 'Visayas'],
                    ['name' => 'Kalawag', 'type' => 'regional', 'region' => 'Mindanao'],
                    ['name' => 'Kunyit', 'type' => 'local', 'region' => 'Sulu']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Understory Cover Crop', 'description' => 'Grows well under tree canopy, enhancing soil organic layer.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Spice Crop & Natural Colorant', 'description' => 'Key culinary ingredient in Filipino Bringhe, curry, and natural yellow food dye.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Turmeric Tea & Topical Paste', 'description' => 'Warm rhizome tea (Salabat variant) drunk for inflammation, arthritis, and liver wellness.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Curcumin Antioxidant & Anti-inflammatory', 'description' => 'Curcuminoid compounds extensively researched for COX-2 inhibition and antioxidant activity.', 'evidence' => 'SUPPORTED_BY_SOME_RESEARCH']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'YES',
                    'traditional_uses_text' => 'Rhizome boiled for sore throat, joint pain, digestive upset, and skin wound topical poultice.',
                    'scientific_evidence_text' => 'Curcuminoids display documented anti-inflammatory and antioxidant properties in numerous clinical studies.',
                    'active_compounds' => 'Curcumin, demethoxycurcumin, bisdemethoxycurcumin, turmerones.',
                    'known_risks' => 'High doses may trigger acid stomach or gall bladder contractions in patients with gallstones.',
                    'known_interactions' => 'May potentiate blood thinners (Warfarin, Aspirin); exercise caution before surgery.',
                    'toxic_parts' => 'None.',
                    'preparation_risks' => 'Stains skin and utensils yellow.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'Patients with bile duct obstruction or gallstones should avoid high-dose turmeric supplements.',
                    'toxic_parts' => 'None.',
                    'look_alike_species' => 'Curcuma zedoaria (White Turmeric / Barak)',
                    'look_alike_distinction' => 'Barak rhizome is light pale yellowish-white inside with a purple stripe along the central midrib of the leaf.',
                    'warning_text' => 'SAFE CULINARY AND HERBAL RHIZOME.'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Listed',
                    'threatened_status' => 'Safe',
                    'protected_status' => 'Widely Farmed',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'None.'
                ],
                'sources' => [
                    ['name' => 'PITAHC Traditional Health Monograph', 'url' => 'https://pitahc.gov.ph', 'type' => 'government']
                ]
            ],
            [
                'scientific_name' => 'Plectranthus scutellarioides',
                'primary_common_name' => 'Mayana',
                'kingdom' => 'Plantae',
                'family' => 'Lamiaceae',
                'genus' => 'Plectranthus',
                'species' => 'scutellarioides',
                'native_status' => 'NATIVE',
                'habitat' => 'Home gardens, shaded slopes, forest understory, parks.',
                'philippine_distribution' => 'Commonly grown and found throughout the entire archipelago.',
                'elevation_range' => '0 - 1500 m above sea level',
                'forest_type' => 'Shaded moist gardens and secondary forest edges',
                'leaf_type' => 'Simple, ovate, membranous, vividly colored (purple, red, yellow, green)',
                'leaf_arrangement' => 'Opposite',
                'leaf_margin' => 'Crenate or serrate',
                'leaf_apex' => 'Acuminate',
                'leaf_base' => 'Cuneate to rounded',
                'venation' => 'Pinnate',
                'growth_habit' => 'Erect succulent herbaceous plant 30-80 cm tall with square stems',
                'bark_description' => 'Fleshy quadrangular 4-angled stem.',
                'flower_description' => 'Small blue or pale violet two-lipped flowers in terminal spikes.',
                'fruit_description' => 'Tiny nutlets.',
                'distinctive_markings' => 'Vivid purple/magenta variegated heart leaves; 4-angled square fleshy stem; crushed leaf poultice for bruises.',
                'images' => [
                    ['file_path' => 'assets/images/species/mayana_leaf.jpg', 'filename' => 'mayana_leaf.jpg', 'type' => 'leaf'],
                    ['file_path' => 'assets/images/species/mayana_plant.jpg', 'filename' => 'mayana_plant.jpg', 'type' => 'whole_plant']
                ],
                'names' => [
                    ['name' => 'Mayana', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Coleus / Painted Nettle', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Baddang', 'type' => 'local', 'region' => 'Ilocos'],
                    ['name' => 'Laponya', 'type' => 'regional', 'region' => 'Visayas'],
                    ['name' => 'Salingkugi', 'type' => 'regional', 'region' => 'Mindanao']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Ornamental Pollinator Shrub', 'description' => 'Flowers feed native butterflies and small garden bees.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Popular Foliage Plant', 'description' => 'Planted for colorful foliage landscaping in Philippine households.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Bruise & Hematoma Poultice', 'description' => 'Crushed warm purple leaves applied over sprains, bruises, swelling, and headache temples.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Rosmarinic Acid & Anthocyanin Content', 'description' => 'High concentration of antioxidant rosmarinic acid and antimicrobial flavonoids.', 'evidence' => 'SUPPORTED_BY_SOME_RESEARCH']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'TRADITIONALLY_USED',
                    'traditional_uses_text' => 'Crushed fresh leaves applied topically for contusions, bruises, sprains, and minor skin cuts.',
                    'scientific_evidence_text' => 'Exhibits antioxidant and antimicrobial action in vitro due to high flavonoid and rosmarinic acid levels.',
                    'active_compounds' => 'Rosmarinic acid, coleonols, anthocyanins, flavonoids.',
                    'known_risks' => 'None for topical poultice.',
                    'known_interactions' => 'None reported.',
                    'toxic_parts' => 'None.',
                    'preparation_risks' => 'Wash leaves before heating for poultice application.'
                ],
                'safety' => [
                    'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                    'primary_warning' => 'Topical poultice usage only. Do not consume raw stems in excessive quantities.',
                    'toxic_parts' => 'None.',
                    'look_alike_species' => 'Perilla frutescens (Shiso)',
                    'look_alike_distinction' => 'Perilla frutescens leaves have a strong distinct anise/mint aroma and serrate dark purple margins.',
                    'warning_text' => 'TOPICAL POULTICE FOR BRUISES AND SWELLING.'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Listed',
                    'threatened_status' => 'Safe',
                    'protected_status' => 'Commonly Cultivated',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'None.'
                ],
                'sources' => [
                    ['name' => 'Philippine Ethnobotanical Plant Database', 'url' => 'https://stii.dost.gov.ph', 'type' => 'government']
                ]
            ],
            [
                'scientific_name' => 'Annona muricata',
                'primary_common_name' => 'Guyabano',
                'kingdom' => 'Plantae',
                'family' => 'Annonaceae',
                'genus' => 'Annona',
                'species' => 'muricata',
                'native_status' => 'NATURALIZED',
                'habitat' => 'Lowland orchards, backyards, secondary agricultural forests.',
                'philippine_distribution' => 'Cultivated in all island groups of the Philippines.',
                'elevation_range' => '0 - 1000 m above sea level',
                'forest_type' => 'Secondary lowland agricultural groves',
                'leaf_type' => 'Simple, obovate to oblong, glossy dark green upper, smooth',
                'leaf_arrangement' => 'Alternate',
                'leaf_margin' => 'Entire',
                'leaf_apex' => 'Acute to acuminate',
                'leaf_base' => 'Cuneate',
                'venation' => 'Pinnate',
                'growth_habit' => 'Small evergreen fruit tree 3-8 meters tall',
                'bark_description' => 'Smooth gray or dark brown bark.',
                'flower_description' => 'Large yellowish-green 3-petaled thick fleshy flowers on trunk or branches (cauliflory).',
                'fruit_description' => 'Large heart-shaped dark green fruit with soft pliable spines and juicy sour-sweet white pulp.',
                'distinctive_markings' => 'Pliable spiky green fruit; glossy dark green leaves with pungent aromatic scent when crushed.',
                'images' => [
                    ['file_path' => 'assets/images/species/guyabano_fruit.jpg', 'filename' => 'guyabano_fruit.jpg', 'type' => 'fruit'],
                    ['file_path' => 'assets/images/species/guyabano_leaf.jpg', 'filename' => 'guyabano_leaf.jpg', 'type' => 'leaf']
                ],
                'names' => [
                    ['name' => 'Guyabano', 'type' => 'tagalog', 'region' => 'National'],
                    ['name' => 'Soursop', 'type' => 'english', 'region' => 'International'],
                    ['name' => 'Bayubana', 'type' => 'local', 'region' => 'Ilocos'],
                    ['name' => 'Guayabano', 'type' => 'regional', 'region' => 'Visayas'],
                    ['name' => 'Yabana', 'type' => 'local', 'region' => 'Cagayan']
                ],
                'uses' => [
                    ['category' => 'ecological', 'title' => 'Orchard Ecosystem Tree', 'description' => 'Provides fruit for local birds and bats.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'agricultural', 'title' => 'Commercial Fruit & Juice Crop', 'description' => 'Cultivated widely for fresh fruit, nectar, smoothies, and jelly products.', 'evidence' => 'ESTABLISHED'],
                    ['category' => 'traditional', 'title' => 'Sedative & Anti-hypertension Tea', 'description' => 'Leaf decoction drunk for sedation, smooth sleep, fever, and blood pressure control.', 'evidence' => 'TRADITIONAL_USE_ONLY'],
                    ['category' => 'scientific', 'title' => 'Annonaceous Acetogenins Research', 'description' => 'Contains acetogenins studied in vitro for cytotoxic effects; clinical cancer claims remain UNPROVEN and unapproved.', 'evidence' => 'PRELIMINARY_EVIDENCE']
                ],
                'medicinal' => [
                    'is_recognized_medicinal' => 'TRADITIONALLY_USED',
                    'traditional_uses_text' => 'Leaves boiled for high blood pressure, calming anxiety, fever relief, and joint swelling.',
                    'scientific_evidence_text' => 'In-vitro lab studies show acetogenin cytotoxicity against cell lines, but clinical efficacy in humans IS NOT established. FDA warns against false cancer cure claims.',
                    'active_compounds' => 'Annonaceous acetogenins (annonacin, muricin), alkaloids, quercetin.',
                    'known_risks' => 'Chronic high consumption of seeds or concentrated leaf extracts containing annonacin linked to neurotoxicity (atypical Parkinsonism).',
                    'known_interactions' => 'May potentiate blood pressure medication.',
                    'toxic_parts' => 'SEEDS ARE TOXIC. Avoid swallowing crushed seeds.',
                    'preparation_risks' => 'Do not boil or ingest crushed seeds.'
                ],
                'safety' => [
                    'safety_category' => 'CAUTION',
                    'primary_warning' => 'DO NOT CONSUME GUYABANO AS A SUBSTITUTE FOR CLINICAL CANCER TREATMENT. Seeds contain toxic neurotoxins.',
                    'toxic_parts' => 'Seeds contain annonacin neurotoxin.',
                    'look_alike_species' => 'Annona squamosa (Atis / Sugar Apple)',
                    'look_alike_distinction' => 'Atis has knobby segmented light-green fruit without spiky spines, and smaller lighter green leaves.',
                    'warning_text' => 'DO NOT CONSUME SEEDS. FRUIT PULP IS SAFE AND NUTRITIOUS.'
                ],
                'conservation' => [
                    'iucn_status' => 'Least Concern (LC)',
                    'denr_status' => 'Not Listed',
                    'threatened_status' => 'Safe',
                    'protected_status' => 'Widely Cultivated',
                    'cites_status' => 'Not Listed',
                    'collection_restrictions' => 'None.'
                ],
                'sources' => [
                    ['name' => 'FDA Philippines Advisory on Herbal Product Claims', 'url' => 'https://fda.gov.ph', 'type' => 'government'],
                    ['name' => 'Memorial Sloan Kettering Cancer Center Integrative Medicine Guide - Soursop', 'url' => 'https://mskcc.org', 'type' => 'academic']
                ]
            ]
        ];

        foreach ($plantsData as $pData) {
            $stmt = $pdo->prepare("INSERT INTO plants (
                scientific_name, primary_common_name, kingdom, family, genus, species,
                native_status, habitat, philippine_distribution, elevation_range, forest_type,
                leaf_type, leaf_arrangement, leaf_margin, leaf_apex, leaf_base, venation,
                growth_habit, bark_description, flower_description, fruit_description, distinctive_markings
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $pData['scientific_name'], $pData['primary_common_name'], $pData['kingdom'], $pData['family'], $pData['genus'], $pData['species'],
                $pData['native_status'], $pData['habitat'], $pData['philippine_distribution'], $pData['elevation_range'], $pData['forest_type'],
                $pData['leaf_type'], $pData['leaf_arrangement'], $pData['leaf_margin'], $pData['leaf_apex'], $pData['leaf_base'], $pData['venation'],
                $pData['growth_habit'], $pData['bark_description'], $pData['flower_description'], $pData['fruit_description'], $pData['distinctive_markings']
            ]);

            $plantId = $pdo->lastInsertId();

            // Images
            if (!empty($pData['images'])) {
                $stmtImg = $pdo->prepare("INSERT INTO plant_images (plant_id, file_path, original_filename, image_type, mime_type, file_size) VALUES (?, ?, ?, ?, 'image/jpeg', 102400)");
                foreach ($pData['images'] as $img) {
                    $stmtImg->execute([$plantId, $img['file_path'], $img['filename'], $img['type']]);
                }
            }

            // Names
            $stmtName = $pdo->prepare("INSERT INTO plant_names (plant_id, name, name_type, language_region, verified_status) VALUES (?, ?, ?, ?, 'verified')");
            foreach ($pData['names'] as $nm) {
                $stmtName->execute([$plantId, $nm['name'], $nm['type'], $nm['region']]);
            }

            // Uses
            $stmtUse = $pdo->prepare("INSERT INTO plant_uses (plant_id, use_category, title, description, evidence_level) VALUES (?, ?, ?, ?, ?)");
            foreach ($pData['uses'] as $us) {
                $stmtUse->execute([$plantId, $us['category'], $us['title'], $us['description'], $us['evidence']]);
            }

            // Medicinal
            $stmtMed = $pdo->prepare("INSERT INTO plant_medicinal_information (
                plant_id, is_recognized_medicinal, traditional_uses_text, scientific_evidence_text,
                active_compounds, known_risks, known_interactions, toxic_parts, preparation_risks
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $med = $pData['medicinal'];
            $stmtMed->execute([
                $plantId, $med['is_recognized_medicinal'], $med['traditional_uses_text'], $med['scientific_evidence_text'],
                $med['active_compounds'], $med['known_risks'], $med['known_interactions'], $med['toxic_parts'], $med['preparation_risks']
            ]);

            // Safety
            $stmtSafe = $pdo->prepare("INSERT INTO plant_safety (
                plant_id, safety_category, primary_warning, toxic_parts, look_alike_species, look_alike_distinction, warning_text
            ) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $safe = $pData['safety'];
            $stmtSafe->execute([
                $plantId, $safe['safety_category'], $safe['primary_warning'], $safe['toxic_parts'],
                $safe['look_alike_species'], $safe['look_alike_distinction'], $safe['warning_text']
            ]);

            // Conservation
            $stmtCons = $pdo->prepare("INSERT INTO plant_conservation (
                plant_id, iucn_status, denr_status, threatened_status, protected_status, cites_status, collection_restrictions
            ) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $cons = $pData['conservation'];
            $stmtCons->execute([
                $plantId, $cons['iucn_status'], $cons['denr_status'], $cons['threatened_status'],
                $cons['protected_status'], $cons['cites_status'], $cons['collection_restrictions']
            ]);

            // Sources
            $stmtSrc = $pdo->prepare("INSERT INTO plant_sources (
                plant_id, fact_type, source_name, source_url, source_type
            ) VALUES (?, 'general_botany', ?, ?, ?)");
            foreach ($pData['sources'] as $src) {
                $stmtSrc->execute([$plantId, $src['name'], $src['url'], $src['type']]);
            }
        }
    }
}
