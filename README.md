# FucqEncode

## Overview
`FucqEncode` is a PHP class developed by Tristan McGowan (tristan@ipspy.net), which implements the advanced 'Fucq' encoding and decoding algorithm. Designed specifically for JSON data, this algorithm, standing for "Frequently Used Character Quantification", provides an efficient method for data compression and encoding. It is uniquely paired with PHP's native `gzencode` and `gzdecode` functions for optimal compression results.

**Scalable Compression:** The `FucqEncode` algorithm becomes more efficient with larger data sets, achieving a higher compression ratio. This characteristic of scalable compression indicates that the algorithm's efficiency improves as the size of the data increases. It is particularly valuable in applications where data sizes vary significantly, demonstrating the algorithm's capability to handle large data effectively.

## Features
- **Advanced JSON Compression:** Tailor-made for compressing JSON data.
- **Integration with Gzencode/Gzdecode:** Utilizes PHP's native compression functions for maximal efficiency.
- **Scalable Compression:** Enhanced effectiveness with increasing data size.
- **GET Request Friendly:** Encoded data is suitable for transmission via GET requests.

## Installation
Include `FucqEncode.php` in your PHP project:

\```php
require_once 'path/to/FucqEncode.php';
\```

## Usage
To use `FucqEncode` for encoding and decoding JSON data, follow this example:

\```php
$fucqEncoder = new FucqEncode();

// Example JSON string
$jsonString = '{"name": "John Doe", "age": 30}';

// Encoding the JSON string
$encodedString = $fucqEncoder->fucqEncodeAlgo($jsonString);

// Decoding the encoded string
$decodedString = $fucqEncoder->fucqDecodeAlgo($encodedString);

// Verify the encoding and decoding
if ($jsonString === $decodedString) {
    echo "Encoding and Decoding successful!";
} else {
    echo "Error in the process.";
}
\```

## Test Results
The algorithm shows promising compression results in initial tests:

### Test 1
- Input Length: 85731
- FucqEncoded Length: 52620, Compression Ratio: 1.629
- Status: COMPRESSED

### Test 2
- Input Length: 85851
- FucqEncoded Length: 52740, Compression Ratio: 1.628
- Status: COMPRESSED

### Test 3
- Input Length: 85953
- FucqEncoded Length: 52622, Compression Ratio: 1.633
- Status: COMPRESSED

## Author
`FucqEncode` was developed by Tristan McGowan (tristan@ipspy.net). Contributions are welcome to further enhance its capabilities.

## License
`FucqEncode` is released under the [MIT License](https://opensource.org/licenses/MIT).
