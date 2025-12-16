
<?php

// Function to process the POST data and return the response
function processRequest($request_data) {
    // Here, you can process the POST data as per your requirements.
    // For simplicity, I'm assuming you will receive the data as JSON.
    // You may need to modify this based on your actual data input.
    // For example, you can use $_POST['key_name'] to access form data.
    
    // Process the request data (you can add any business logic here)

    // Prepare the response data
    $response_data = array(
        "status" => "success",
        "Status" => "processing",
        "network" => "MTN",
        "quantity" => "1",
        "msg" =>  "45677788888886667888",
        "pin" => "2541455678" ,
        "serial" => "5434556787_90",
        "load_pin" => "*556#",
        "price" => 200,
        "Amount" => 200,
        "oldbal" => 500,
        "newbal" => 300
    );

    return $response_data;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $request_body = file_get_contents('php://input');
    
    // Convert JSON to PHP array
    $request_data = json_decode($request_body, true);

    // Process the request and get the response
    $response = processRequest($request_data);

    // Set the content type to JSON
    header('Content-Type: application/json');

    // Return the response as JSON
    echo json_encode($response);
} else {
    // Handle other HTTP methods (optional)
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("status" => "error", "message" => "Method not allowed."));
}
