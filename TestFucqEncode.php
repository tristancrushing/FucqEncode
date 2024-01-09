<?php

require_once 'FucqEncode.php';

class TestFucqEncode {
    private FucqEncode $fucqEncoder;

    public function __construct() {
        $this->fucqEncoder = new FucqEncode();
    }

    private function generateRandomString($length = 10): string {
        return base64_encode(random_bytes($length));
    }

    private function calculateCompressionRatio($originalLength, $encodedLength): float {
        return $originalLength / $encodedLength;
    }

    private function testEncoding(string $input): void {
        // Original length
        $originalLength = strlen($input);

        // FucqEncode
        $fucqEncoded = $this->fucqEncoder->fucqEncodeAlgo($this->fucqEncoder->encode($input));
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

        // Output the results for comparison
        echo "Input String: $input <br/>" . PHP_EOL;
        echo "Input Length: $originalLength <br/>" . PHP_EOL;
        echo "FucqEncoded Length: $fucqLength, Compression Ratio: $fucqRatio <br/>" . PHP_EOL;
        echo "Base64 Encoded Length: $base64Length, Compression Ratio: $base64Ratio <br/>" . PHP_EOL;
        echo "Hex Encoded Length: $hexLength, Compression Ratio: $hexRatio <br/><br/><br/>" . PHP_EOL;
        echo PHP_EOL;
    }

    public function runTests(): void {
        echo "Testing Compression Ratios and Character Counts:" . PHP_EOL;
        $this->testEncoding($this->generateRandomString(10));  // 10 bytes
        $this->testEncoding($this->generateRandomString(100)); // 100 bytes
        $this->testEncoding($this->generateRandomString(1000)); // 1000 bytes
    }
}

// Create a new instance of the test class and run the tests
$test = new TestFucqEncode();
$test->runTests();
