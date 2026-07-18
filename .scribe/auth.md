# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_SANCTUM_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Use <code>POST /api/v1/auth/login</code> or create a named token under <b>Profile → API Tokens</b>, then send it as <code>Authorization: Bearer &lt;token&gt;</code>.
