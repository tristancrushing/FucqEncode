# FucqEncode PHP Class

## Overview
FucqEncode is a PHP class that implements a unique string encoding and decoding algorithm, named 'Frequently Used Character Quantification' (Fucq). It provides a multi-layered `bin2hex` encoding approach with a novel method to quantify character repetitions. The class is designed for educational purposes, showcasing advanced encoding techniques in PHP.

## Features
- **Multi-Layered Encoding**: Implements multiple layers of `bin2hex` encoding, stopping when the encoded string contains only 7 unique digits.
- **Custom Encoding Algorithm**: Includes a custom encoding algorithm (`fucqEncodeAlgo`) that transforms encoded strings into a sequence based on consecutive character counts.
- **Decoding Functionality**: Provides methods to reverse the encoding process, with limitations in reconstructing the original string without additional information.

## Installation
Simply download the `FucqEncode.php` file and include it in your PHP project:
```php
require_once 'path/to/FucqEncode.php';
```

## Usage
Instantiate the `FucqEncode` class and use its methods to encode and decode strings:
```php
$fucqEncoder = new FucqEncode();

// Encoding a string
$encoded = $fucqEncoder->encode('Your String Here');

// Applying the fucqEncode algorithm
$fucqEncoded = $fucqEncoder->fucqEncodeAlgo($encoded);

// Decoding the fucqEncoded string (this step requires the original character information or an appropriate logic)
$fucqDecoded = $fucqEncoder->fucqDecodeAlgo($fucqEncoded);

// Decoding the string from hex
$decoded = $fucqEncoder->decode($fucqDecoded);
```

## Methods
- `encode(string $input): string`: Encodes a given string using multi-layered `bin2hex`.
- `decode(string $input): string`: Decodes the given string, reversing the `bin2hex` encoding.
- `fucqEncodeAlgo(string $encodedString): string`: Applies the Fucq encoding algorithm to the encoded string.
- `fucqDecodeAlgo(string $encodedString): string`: Decodes a string processed by `fucqEncodeAlgo`.

## Contributing
Contributions, issues, and feature requests are welcome. Feel free to check [issues page](link-to-issues-page) if you want to contribute.

## License
Distributed under the MIT License. See `LICENSE` for more information.

## Author
Tristan McGowan

## Contact
Tristan McGowan - [tristan@ipspy.net](mailto:tristan@ipspy.net)

Project Link: [https://github.com/tristancrusing/FucqEncode](https://github.com/tristancrusing/FucqEncode)
