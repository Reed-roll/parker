Parking microservice (CI4)

Quick start:

1. Ensure writable/database exists (it was created as placeholder).

2. Configure DB in `app/Config/Database.php` or override via `.env`:

   - `database.default.DBDriver=SQLite3`
   - `database.default.database=WRITEPATH.'database/parking.db'`

3. Run migrations and seed (from project root):

```bash
php spark migrate
php spark db:seed ParkingSeeder
```

4. Example API calls (no auth yet — use header `X-User-Id: 1`):

- Start session: POST /api/tickets/start
- End session: POST /api/tickets/{id}/end
- Pay: POST /api/tickets/{id}/pay

5. Configure external payment microservice via environment:

- `PAYMENT_SERVICE_URL` (e.g., https://payments.example)
- `PAYMENT_SERVICE_KEY`

If `PAYMENT_SERVICE_URL` is not set, payments will be simulated.
