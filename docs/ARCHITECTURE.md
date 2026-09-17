# Puno at Halaman AI - Technical Architecture & System Design

## 1. Executive Overview
"Puno at Halaman AI" is a web application written in PHP 8.2+ designed for botanical species identification, ethnobotanical reference, field biodiversity documentation, and educational training within the Philippine archipelago.

## 2. Core Architecture
The system follows a Model-View-Controller (MVC) architectural pattern:

```
[ HTTP Request ]
       │
       ▼
[ public/index.php ] ──▶ [ App\Helpers\Router ]
                               │
            ┌──────────────────┴──────────────────┐
            ▼                                     ▼
[ Web Controllers ]                    [ API Controllers ]
(HomeController, etc.)                 (IdentifyController, etc.)
            │                                     │
            ▼                                     ▼
    [ Views Engine ]                     [ AI & Services ]
   (Bilingual EN/FIL)                    ├── UploadService
                                         ├── ImageQualityAnalyzer
                                         └── AI Plant Identifier Factory
                                                     │
                                                     ▼
                                        [ RAG Knowledge Retriever ]
                                                     │
                                                     ▼
                                        [ PDO Database Connection ]
                                        (MariaDB / MySQL Database)
```

## 3. Modular AI Vision Pipeline
AI vision providers implement `App\AI\AIPlantIdentifierInterface`:
1. **MockPlantIdentifier**: Offline fallback provider for zero-API-key development and testing.
2. **OpenAIPlantIdentifier**: Vision model integration using GPT-4o with structured JSON responses.
3. **GeminiPlantIdentifier**: Google Gemini Vision API wrapper.

## 4. RAG Knowledge Enrichment Framework
When candidate species names are returned by AI vision providers, `RAGKnowledgeRetriever` fetches authoritative records from the local MariaDB database to inject:
- Official Philippine native/endemic/introduced classification.
- Tagalog and regional local names.
- Scientifically validated active compounds and evidence levels.
- Primary safety warnings, toxic plant parts, and look-alike distinctions.
- Conservation status (IUCN, DENR DAO 2017-11, CITES).
