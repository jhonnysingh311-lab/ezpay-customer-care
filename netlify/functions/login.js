const { neon } = require('@neondatabase/serverless');

exports.handler = async (event, context) => {
    const headers = {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': 'Content-Type',
        'Access-Control-Allow-Methods': 'POST, OPTIONS'
    };

    if (event.httpMethod === 'OPTIONS') {
        return { statusCode: 200, headers, body: '' };
    }

    if (event.httpMethod !== 'POST') {
        return { statusCode: 405, headers, body: "Method Not Allowed" };
    }

    try {
        const sql = neon(process.env.NETLIFY_DATABASE_URL || process.env.DATABASE_URL);
        const { phone, password } = JSON.parse(event.body);

        if (!phone || !password) {
            return { statusCode: 400, headers, body: JSON.stringify({ error: "Missing fields" }) };
        }

        // Check user
        const users = await sql`SELECT * FROM users WHERE phone = ${phone}`;
        const user = users[0];

        if (user) {
            // Verify Password (Plain Text)
            if (user.password === password) {
                return {
                    statusCode: 200,
                    headers,
                    body: JSON.stringify({ success: true, userId: user.id })
                };
            } else {
                return {
                    statusCode: 401,
                    headers,
                    body: JSON.stringify({ error: "Invalid password" })
                };
            }
        } else {
            // Register New User (Plain Text)
            const newUser = await sql`
                INSERT INTO users (phone, password) 
                VALUES (${phone}, ${password}) 
                RETURNING id
            `;
            return {
                statusCode: 200,
                headers,
                body: JSON.stringify({ success: true, userId: newUser[0].id })
            };
        }
    } catch (error) {
        console.error(error);
        return {
            statusCode: 500,
            headers,
            body: JSON.stringify({ error: "Server error", details: error.message })
        };
    }
};
