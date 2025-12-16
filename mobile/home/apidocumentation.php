<!DOCTYPE html>
<html>
<head>
    <title>API Documentation Page</title>
    <style>
        body {
        margin-top: 40px;
            font-family: Arial, sans-serif;
            line-height: 2.5;
            margin: 0;
            padding: 0;
            background-color: white;
            border-radius: 100px ;
        }
        h1 {
            text-align: center;
            
            margin-bottom: 40px;
            margin-top: 50px;
            padding: 20px 0;
            background-color: #5B2DD9;
            color: #fff;
        }
        .container {
            border-radius: 0px ;
            
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9 ;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .endpoint {
            margin-bottom: 20px;
            border: 0px solid #ccc;
            padding: 15px;
            border-radius: 15px;
            background-color: white;
            box-shadow: 0 0 20px #B2B1B6 ;
        }
        .endpoint h2 {
            margin-bottom: 10px;
            color: #5B2DD9 ;
        }
        .endpoint p {
            margin-top: 0;
            margin-bottom: 5px;
        }
        pre {
            background-color: #CDC5E3;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        code {
            color: #5B2DD9 ;
        }
    </style>
</head>
<body>
    
    <h1>API Documentation Page</h1>

    <div class="container">
        <div class="endpoint">
            <h2>Check Balance</h2>
            <p>Endpoint: <code>https://jossyfeydataservice.com.ng/api/user/</code></p>
            <p>Method: GET</p>
            <pre><code>
curl --location 'https://jossyfeydataservice.com.ng/api/user/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'
            </code></pre>
        </div>
        <hr>

        <div class="endpoint">
            <h2>Airtime</h2>
            <p>Endpoint: <code>https://jossyfeydataservice.com.ng/api/airtime/</code></p>
            <p>Method: GET</p>
            <pre><code>
curl --location 'https://jossyfeydataservice.com.ng/api/airtime/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'
            </code></pre>
        </div>
<hr>
        <div class="endpoint">
            <h2>Data</h2>
            <p>Endpoint: <code>https://jossyfeydataservice.com.ng/api/data/</code></p>
            <p>Method: GET</p>
            <pre><code>
curl --location 'https://jossyfeydataservice.com.ng/api/data/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'
            </code></pre>
        </div>
<hr>
        <div class="endpoint">
            <h2>Verify Cable</h2>
            <p>Endpoint: <code>https://jossyfeydataservice.com.ng/api/cabletv/verify/</code></p>
            <p>Method: GET</p>
            <pre><code>
curl --location 'https://jossyfeydataservice.com.ng/api/cabletv/verify/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'
            </code></pre>
        </div>
<hr>
        <div class="endpoint">
            <h2>Cable</h2>
            <p>Endpoint: <code>https://jossyfeydataservice.com.ng/api/cabletv/</code></p>
            <p>Method: GET</p>
            <pre><code>
curl --location 'https://jossyfeydataservice.com.ng/api/cabletv/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'
            </code></pre>
        </div>
<hr>
        <div class="endpoint">
            <h2>Verify Electricity</h2>
            <p>Endpoint: <code>https://jossyfeydataservice.com.ng/api/electricity/verify/</code></p>
            <p>Method: GET</p>
            <pre><code>
curl --location 'https://jossyfeydataservice.com.ng/api/electricity/verify/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'
            </code></pre>
        </div>
<hr>
        <div class="endpoint">
            <h2>Electricity</h2>
            <p>Endpoint: <code>https://jossyfeydataservice.com.ng/api/electricity/</code></p>
            <p>Method: GET</p>
            <pre><code>
curl --location 'https://jossyfeydataservice.com.ng/api/electricity/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'
            </code></pre>
        </div>
<hr>
        <div class="endpoint">
            <h2>Exam Pin</h2>
            <p>Endpoint: <code>https://jossyfeydataservice.com.ng/api/exam/</code></p>
            <p>Method: GET</p>
            <pre><code>
curl --location 'https://jossyfeydataservice.com.ng/api/exam/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'
            </code></pre>
        </div>
<hr>
        <div class="endpoint">
            <h2>Data Pin</h2>
            <p>Endpoint: <code>https://jossyfeydataservice.com.ng/api/datapin/</code></p>
            <p>Method: GET</p>
            <pre><code>
curl --location 'https://jossyfeydataservice.com.ng/api/datapin/' \
--header 'Authorization: Token your-api-key-here' \
--header 'Content-Type: application/json'
            </code></pre>
        </div>
    </div>
</body>
</html>
