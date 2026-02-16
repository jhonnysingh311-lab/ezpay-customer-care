const { neon } = require('@neondatabase/serverless');
const bcrypt = require('bcryptjs');

exports.handler = async (event, context) => {
    // Only allow POST
    if (event.httpMethod !== 'POST') {
        return { statusCode: 405, body: "Method Not Allowed" };
    }

    try {
        const sql = neon(process.env.DATABASE_URL);
        const { phone, password } = JSON.parse(event.body);

        if (!phone || !password) {
            return { statusCode: 400, body: JSON.stringify({ error: "Missing fields" }) };
        }

        // Check user
        const users = await sql`SELECT * FROM users WHERE phone = ${phone}`;
        const user = users[0];

        if (user) {
            // Verify Password
            const match = await bcrypt.compare(password, user.password);
            if (match) {
                return {
                    statusCode: 200,
                    body: JSON.stringify({ success: true, userId: user.id })
                };
            } else {
                return {
                    statusCode: 401,
                    body: JSON.stringify({ error: "Invalid password" })
                };
            }
        } else {
            // Register New User
            const hashedPassword = await bcrypt.hash(password, 10);
            const newUser = await sql`
                INSERT INTO users (phone, password) 
                VALUES (${phone}, ${hashedPassword}) 
                RETURNING id
            `;
            return {
                statusCode: 200,
                body: JSON.stringify({ success: true, userId: newUser[0].id })
            };
        }
    } catch (error) {
        console.error(error);
        return {
            statusCode: 500,
            body: JSON.stringify({ error: "Server error", details: error.message })
        };
    }
};
