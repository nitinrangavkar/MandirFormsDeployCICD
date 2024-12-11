<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Form</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 600px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        p {
            font-size: 1em;
            margin: 10px 0;
        }

        .highlight {
            font-weight: bold;
        }

        form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9em;
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }

            h1 {
                font-size: 1.2em;
            }

            p {
                font-size: 0.9em;
            }

            button {
                padding: 8px;
            }
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>परमपूज्य आईंच्या चरणी शिरसाष्टांग दंडवत</h1>
        <p>आपल्या <span class="highlight">दिनांक (तारीख)</span> शिबीराचे आयोजन केले आहे</p>
        <p>दिनांक: 30/05/24 ते दिनांक: 05/06/24</p>
        <p>इच्छित तिथे कृपया परतावा लिहावा.</p>
        <p>खाले भोजनाचा वेळ <span class="highlight">सकाळी: 8 वाजता</span></p>
        <p>सकाळच्या नाश्ता / दुपारचा प्रसाद / रात्रीचा प्रसाद</p>

        <form>
            <div class="form-group">
                <label for="name">बांधू व भगीनींची नावे</label>
                <input type="text" id="name" name="name" placeholder="Enter name">
            </div>

            <div class="form-group">
                <label for="service">सेवा</label>
                <select id="service" name="service">
                    <option value="शिक्षक">शिक्षक</option>
                    <option value="मिटी">मिटी</option>
                    <option value="इतर">इतर</option>
                </select>
            </div>

            <div class="form-group">
                <label for="number">क्रमांक</label>
                <input type="number" id="number" name="number" placeholder="Enter number">
            </div>

            <div class="form-group">
                <label for="dates">दिनांक</label>
                <input type="text" id="dates" name="dates" placeholder="Enter dates">
            </div>

            <div class="form-group">
                <label for="time">तारीख</label>
                <input type="text" id="time" name="time" placeholder="Enter time">
            </div>

            <div class="form-group">
                <label for="activity">अध्याय वाचण</label>
                <input type="text" id="activity" name="activity" placeholder="Enter activity">
            </div>

            <div class="form-group">
                <label for="contact">संपर्कासाठी दूरध्वनी क्रमांक</label>
                <input type="text" id="contact" name="contact" placeholder="Enter contact number">
            </div>

            <div class="form-group">
                <button type="submit">Submit</button>
            </div>
        </form>

        <footer>
            <p>परमाय स्त्रोत वस्त्र गने</p>
            <p>अंतराळ उपाध्यक्ष - दिवा</p>
            <p>आपली सेवेकरी</p>
            <p>मुख्य संयोजक / गुरुभगिनी</p>
        </footer>
    </div>
</body>
</html>
