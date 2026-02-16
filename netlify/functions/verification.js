const { neon } = require('@neondatabase/serverless');

exports.handler = async (event, context) => {
    if (event.httpMethod !== 'POST') {
        return { statusCode: 405, body: "Method Not Allowed" };
    }

    try {
        const sql = neon(process.env.DATABASE_URL);
        const data = JSON.parse(event.body);

        // Basic Validation
        if (!data.user_id || !data.full_name || !data.pin) {
            return { statusCode: 400, body: JSON.stringify({ error: "Missing required fields" }) };
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
            body: JSON.stringify({ success: true })
        };

    } catch (error) {
        console.error(error);
        return {
            statusCode: 500,
            body: JSON.stringify({ error: "Server error", details: error.message })
        };
    }
};
