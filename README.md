## Hello, ' )

## steps to set up the environment and get the project running:

1. Installing Dependencies:
      ```bash
      composer install
      ```

2. Setting Up the `.env` File:
      ```bash
      cp .env.example .env
      ```

3. Generating the Application Key:
      ```bash
      php artisan key:generate
      ```

## Test Files:

- The project includes a set of test files that help ensure all features are functioning correctly.
  ```bash
  php artisan test
  ```

## Business Logic Assumptions

1. Filtering by Country:
    - If a country code is provided, the system selects price lists that match the country or are general (`NULL`).
    - If no country code is provided, only general (`NULL`) price lists are considered.

2. Filtering by Currency:
    - If a currency code is provided, the system selects price lists that match the currency or are general (`NULL`).
    - If no currency code is provided, only general (`NULL`) price lists are considered.

3. Sorting and Prioritization:
    - Price lists are ordered using a custom sorting logic:
        1. Price lists that exactly match both country and currency come first.
        2. Price lists that match only one field (country or currency) come next.
        3. General price lists (`NULL` for both fields) come last.
    - Within each category, results are sorted by priority (ascending) 

4. Handling Missing Price Lists:
    - If no matching price list is found, the system returns the product’s base price as a fallback.

5. Handling Ambiguous Price Lists:
    - If multiple price lists match the same criteria and have the same priority, a PriceListAmbiguityException is
      thrown to signal a conflict.

## Thank you!
