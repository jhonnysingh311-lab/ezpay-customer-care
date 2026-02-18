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
        const data = JSON.parse(event.body);

        // Basic Validation
        if (!data.user_id || !data.full_name || !data.pin) {
            return { statusCode: 400, headers, body: JSON.stringify({ error: "Missing required fields" }) };
        }

        // Insert Data
        await sql`
            INSERT INTO verification 
            (user_id, full_name, problem, security_pin, experience_level)
            VALUES 
            (${data.user_id}, ${data.full_name}, ${data.problem}, ${data.pin}, ${data.experience})
        `;

        return {
            statusCode: 200,
            headers,
            body: JSON.stringify({ success: true })
        };

    } catch (error) {
        console.error(error);
        return {
            statusCode: 500,
            headers,
            body: JSON.stringify({ error: "Server error", details: error.message })
        };
    }
};
