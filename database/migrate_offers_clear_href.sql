-- Homepage offers carousel: remove slide links (run once on existing databases)
UPDATE homepage_offers SET href = '' WHERE href <> '';
