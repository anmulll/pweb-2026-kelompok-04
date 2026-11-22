# Entity Relationship Diagram (ERD) — iStore v3.1

- **Penyusun:** putri (Database & QA Engineer)
- **Status:** Draft untuk Review Tim (`anmul`, `adel`, `caca`) sebelum finalisasi migration
- **Database Engine:** MySQL 8.4.x (`InnoDB`, `utf8mb4_unicode_ci`)
- **Cakupan:** 9 Tabel Domain (114 Kolom) — terpisah dari tabel bawaan Laravel (`migrations`, `sessions`, `cache`, `jobs`, `failed_jobs`, `password_reset_tokens`)

---

## 1. Diagram Relasi Entitas (Mermaid ERD)

```mermaid
erDiagram
    users ||--o{ promotions : "created_by (1:N)"
    users ||--o| carts : "has_one (1:0..1)"
    users ||--o{ orders : "places (1:N)"

    categories ||--o{ products : "categorizes (1:N)"

    carts ||--o{ cart_items : "contains (1:N)"
    products ||--o{ cart_items : "referenced_in (1:N)"

    orders ||--|{ order_items : "consists_of (1:N)"
    products ||--o{ order_items : "snapshotted_in (1:N)"

    orders ||--|| payments : "settled_by (1:1)"

    users {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at "nullable"
        string password
        enum role "buyer | seller"
        string phone "nullable"
        text default_shipping_address "nullable"
        string remember_token "nullable"
        timestamp created_at
        timestamp updated_at
    }

    categories {
        bigint id PK
        string name
        string slug UK
        text description "nullable"
        boolean is_active "default true"
        timestamp created_at
        timestamp updated_at
    }

    products {
        bigint id PK
        bigint category_id FK "RESTRICT"
        string name
        string slug UK
        string sku UK
        text description "nullable"
        enum condition "new | used"
        decimal price "CHECK > 0"
        int stock "CHECK >= 0"
        string image "nullable (path relatif)"
        boolean is_active "default true"
        enum condition_grade "nullable (A|B|C)"
        tinyint battery_health "nullable (0..100)"
        enum functional_status "nullable"
        text defect_notes "nullable"
        text included_accessories "nullable"
        string warranty_info "nullable"
        timestamp created_at
        timestamp updated_at
    }

    promotions {
        bigint id PK
        bigint created_by FK "users.id"
        string title
        text description "nullable"
        string image "nullable (path relatif)"
        date start_date
        date end_date "CHECK >= start_date"
        boolean is_active "default true"
        timestamp created_at
        timestamp updated_at
    }

    carts {
        bigint id PK
        bigint user_id FK,UK "CASCADE (maks. 1 cart/Buyer)"
        timestamp created_at
        timestamp updated_at
    }

    cart_items {
        bigint id PK
        bigint cart_id FK "CASCADE"
        bigint product_id FK "RESTRICT"
        int quantity "CHECK >= 1"
        timestamp created_at
        timestamp updated_at
    }

    orders {
        bigint id PK
        bigint user_id FK "RESTRICT"
        string order_code UK
        enum status "pending_payment|paid|processing|shipped|completed|cancelled"
        string recipient_name
        string recipient_phone
        text shipping_address
        decimal subtotal
        decimal shipping_cost
        decimal total_amount
        string courier_name "nullable"
        string tracking_number "nullable"
        timestamp stock_restored_at "nullable (idempotency guard)"
        timestamp created_at
        timestamp updated_at
    }

    order_items {
        bigint id PK
        bigint order_id FK "CASCADE"
        bigint product_id FK "RESTRICT"
        string product_name_snapshot
        string sku_snapshot
        enum condition_snapshot "new | used"
        enum condition_grade_snapshot "nullable"
        tinyint battery_health_snapshot "nullable"
        enum functional_status_snapshot "nullable"
        text defect_notes_snapshot "nullable"
        text included_accessories_snapshot "nullable"
        string warranty_info_snapshot "nullable"
        decimal unit_price_snapshot
        int quantity
        decimal line_total
        timestamp created_at
        timestamp updated_at
    }

    payments {
        bigint id PK
        bigint order_id FK,UK "CASCADE (1:1)"
        string gateway_order_id UK
        string provider "doku_sandbox | midtrans_sandbox (read-only)"
        enum status "pending|paid|challenged|expired|failed|cancelled"
        decimal amount
        string gateway_checkout_url "nullable"
        string gateway_token "nullable"
        string last_request_id "nullable"
        timestamp expired_at "nullable"
        timestamp paid_at "nullable"
        json raw_response "nullable"
        timestamp created_at
        timestamp updated_at
    }