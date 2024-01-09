<?php

require_once 'FucqEncode.php';

class TestFucqEncode {
    private FucqEncode $fucqEncoder;
    private bool $outputJson;

    public function __construct(bool $outputJson = false) {
        $this->fucqEncoder = new FucqEncode();
        $this->outputJson = $outputJson;
    }

    private function generateRandomString(int $length = 10): string {
        return base64_encode(random_bytes($length));
    }

    private function calculateCompressionRatio(int $originalLength, int $encodedLength): float {
        return $originalLength / $encodedLength;
    }

    private function testEncoding(string $input): void {
        // Original length
        $originalLength = strlen($input);

        // FucqEncode
        $fucqEncoded = $this->fucqEncoder->fucqEncodeAlgo($input);
        $fucqDecoded = $this->fucqEncoder->fucqDecodeAlgo($fucqEncoded);
        $fucqLength = strlen($fucqEncoded);

        // Base64 Encode
        $base64Encoded = base64_encode($input);
        $base64Length = strlen($base64Encoded);

        // Hex Encode
        $hexEncoded = bin2hex($input);
        $hexLength = strlen($hexEncoded);

        // Compression Ratios
        $fucqRatio = $this->calculateCompressionRatio($originalLength, $fucqLength);
        $base64Ratio = $this->calculateCompressionRatio($originalLength, $base64Length);
        $hexRatio = $this->calculateCompressionRatio($originalLength, $hexLength);

        // Prepare results
        $results = [
            "testPassed" => $input === $fucqDecoded,
            "inputString" => $input,
            "inputLength" => $originalLength,
            "fucqEncodedString" => $fucqEncoded,
            "fucqEncodedLength" => $fucqLength,
            "fucqCompressionRatio" => $fucqRatio,
            "base64EncodedLength" => $base64Length,
            "base64CompressionRatio" => $base64Ratio,
            "hexEncodedLength" => $hexLength,
            "hexCompressionRatio" => $hexRatio
        ];

        // Output the results
        if ($this->outputJson) {
            echo json_encode($results);
        } else {
            $this->outputHtml($results);
        }
    }

    private function outputHtml(array $results): void {
        echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">';
        echo '<div class="container mt-4">';
        echo '<h1 class="mb-4">' . ($results['testPassed'] ? '<span class="text-success">Test Passed!</span>' : '<span class="text-danger">TEST FAILED</span>') . '</h1>';
    
        // Input String
        echo '<div class="mb-3"><strong>Input String:</strong><br/>';
        echo '<textarea class="form-control" style="height: 150px;" readonly>' . htmlspecialchars($results['inputString']) . '</textarea></div>';
    
        // FucqEncoded String
        echo '<div class="mb-3"><strong>FucqEncoded String:</strong><br/>';
        echo '<textarea class="form-control" style="height: 150px;" readonly>' . htmlspecialchars($results['fucqEncodedString']) . '</textarea></div>';
        
        echo '<div class="mb-3"><strong>Input Length:</strong> ' . $results['inputLength'] . '</div>';
    
        // FucqEncoded Length and Ratio
        echo '<div class="mb-3"><strong>FucqEncoded Length:</strong> ' . $results['fucqEncodedLength'];
        echo ', <strong>Compression Ratio:</strong> ' . $results['fucqCompressionRatio'] . '</div>';
        echo '<div class="mb-3 ' . ($results['fucqCompressionRatio'] > 1 ? 'text-success' : 'text-danger') . '">';
        echo ($results['fucqCompressionRatio'] > 1 ? 'COMPRESSED' : 'BLOATED') . '</div>';
    
        // Base64 Encoded Length and Ratio
        echo '<div class="mb-3"><strong>Base64 Encoded Length:</strong> ' . $results['base64EncodedLength'];
        echo ', <strong>Compression Ratio:</strong> ' . $results['base64CompressionRatio'] . '</div>';
        echo '<div class="mb-3 ' . ($results['base64CompressionRatio'] > 1 ? 'text-success' : 'text-danger') . '">';
        echo ($results['base64CompressionRatio'] > 1 ? 'COMPRESSED' : 'BLOATED') . '</div>';
    
        // Hex Encoded Length and Ratio
        echo '<div class="mb-3"><strong>Hex Encoded Length:</strong> ' . $results['hexEncodedLength'];
        echo ', <strong>Compression Ratio:</strong> ' . $results['hexCompressionRatio'] . '</div>';
        echo '<div class="mb-3 ' . ($results['hexCompressionRatio'] > 1 ? 'text-success' : 'text-danger') . '">';
        echo ($results['hexCompressionRatio'] > 1 ? 'COMPRESSED' : 'BLOATED') . '</div>';
    
        echo '</div>';
    }

    public function runTests(): void {
        echo "Testing Compression Ratios and Character Counts:<br/>" . PHP_EOL;
        
        $range = range(0, 9);
        
        foreach ($range as $test) {
            usleep(1500);
            $this->testEncoding(file_get_contents('https://random-data-api.com/api/v2/users?size=100'));
        }
    }
}

// Create a new instance of the test class and run the tests
$test = new TestFucqEncode(false); // Set to false for HTML output
$test->runTests();
