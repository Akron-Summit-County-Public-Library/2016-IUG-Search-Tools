SELECT "fine"."id",
    "patron_view"."barcode",
    "fine"."patron_record_id",
    "fine"."checkout_gmt",
    "fine"."due_gmt",
    "patron_view"."birth_date_gmt",
    ("fine"."item_charge_amt" - "fine"."paid_amt") as past_statute_debt
FROM "fine"
    JOIN "patron_view" ON "patron_view"."id" = "fine"."patron_record_id"
    JOIN "record_metadata" ON "record_metadata"."id" = "fine"."item_record_metadata_id"
    JOIN "item_view" ON "item_view"."record_num" = "record_metadata"."record_num"
WHERE "due_gmt" <= '{$statute} 00:00:00-05'
    AND "item_view"."item_status_code" = 'n'
