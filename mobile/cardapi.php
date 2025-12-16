<!DOCTYPE html>
<html>
<head>
    <title>jsPDF Test</title>
</head>
<body>
    <div>
        <h1>Testing jsPDF Library</h1>
        <p>This is a sample text to include in the PDF.</p>
        <button id="generatePDF">Generate PDF</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
    <script>
        document.getElementById('generatePDF').addEventListener('click', function() {
            const pdf = new jsPDF();
            pdf.text(20, 20, 'Hello, this is a test PDF!');
            pdf.save('test.pdf');
        });
    </script>
</body>
</html>
