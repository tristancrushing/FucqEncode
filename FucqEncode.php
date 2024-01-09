<?php
ini_set('memory_limit', '8192M'); // For Testing Purposes, will lock down memeory requirments when I finalize algo.

/**
 * Class FucqEncode
 * Implements the 'Fucq' encoding and decoding algorithm.
 * 'Fucq' stands for "Frequently Used Character Quantification" encoding.
 * This class offers methods for multi-layered bin2hex encoding with a unique stopping condition,
 * a custom algorithm for transforming encoded strings based on character repetition,
 * and methods for decoding these transformations.
 *
 * The encode method applies bin2hex multiple times and stops if the encoded string contains only 7 unique digits.
 * The decode method reverses this process but requires additional information about the character sequence
 * used in the fucqEncodeAlgo method for accurate reconstruction of the original string.
 */
class FucqEncode {
    private int $maxLayers;

    /**
     * Constructor for the FucqEncode class.
     * Initializes the object with the maximum layers of encoding.
     *
     * @param int $maxLayers The maximum layers of encoding to apply.
     */
    public function __construct(int $maxLayers = 1) {
        $this->maxLayers = $maxLayers;
    }

    public function __run_example_usage() {
      // Usage example
      $fucqEncoder = new FucqEncode();
      
      $stringToEncode = file_get_contents('https://random-data-api.com/api/v2/users?size=10');
      echo "<pre>";
      echo "<h3>\$stringToEncode</h3>";
      print_r($stringToEncode);
      echo "</pre>";
      
      // Applying the fucqEncode algorithm
      $fucqEncoded = $fucqEncoder->fucqEncodeAlgo($stringToEncode);
      echo "<pre>";
      echo "<h3>\$fucqEncoded</h3>";
      print_r($fucqEncoded);
      echo "</pre>";
      
      // Decoding the fucqEncoded string (this step requires the original character information or an appropriate logic)
      $fucqDecoded = $fucqEncoder->fucqDecodeAlgo($fucqEncoded);
      echo "<pre>";
      echo "<h3>\$fucqDecoded</h3>";
      print_r($fucqDecoded);
      echo "</pre>";
      
      if( $stringToEncode === $fucqDecoded )
      {
          echo "<h1 style=\"color:green;\">TEST PASSED!</h1>";
      }
    }

    /**
     * Encodes a given string using multiple layers of bin2hex encoding.
     * Stops the encoding process once the string contains only 7 unique digits.
     *
     * @param string $input The input string to encode.
     * @return string The encoded string.
     */
    public function encode(string $input): string {
        $encoded = $input;

        for ($i = 0; $i < $this->maxLayers; $i++) {
            $encoded = bin2hex($encoded);
        }

        return $encoded;
    }

    /**
     * Decodes a given string by reversing the bin2hex encoding process.
     * Incorporates a check for successful hex2bin conversion to determine if the string is fully decoded.
     *
     * @param string $input The input string to decode.
     * @return string The decoded string, or an error message if decoding fails.
     */
    public function decode(string $input): string {
        $decoded = $input;

        for ($i = 0; $i < $this->maxLayers; $i++) {
            $tempDecoded = hex2bin($decoded);
            if ($tempDecoded === false) {
                // hex2bin failed, indicating the string is fully decoded
                break;
            }
            $decoded = $tempDecoded;
        }

        return $decoded;
    }

     // FucqEncodeAlgo Step 1
    private function encodeStep1(string $encodedString): array {
        $result = [];
        $len = strlen($encodedString);

        for ($i = 0; $i < $len; $i++) {
            $count = 1;
            while ($i < $len - 1 && $encodedString[$i] === $encodedString[$i + 1]) {
                $count++;
                $i++;
                if ($encodedString[$i] !== $encodedString[$i + 1]) {
                    $result[] = $count . ',' . $encodedString[$i];
                }
            }

            if ($count === 1) {
                $result[] = $count . ',' . $encodedString[$i];
            }
        }

        return $result;
    }

    // FucqEncodeAlgo Step 2
    private function encodeStep2(array $fqEncWorkOgArray): array {
        $fqCharMap = [];
        $fqEncWorkOgArrayUnique = array_unique($fqEncWorkOgArray);
        $fqEncString = implode(';', $fqEncWorkOgArray);
    
        if (count($fqEncWorkOgArrayUnique) > 52) {
            return ["error" => "Not enough unique elements to map to each alphabet character"];
        }
    
        $alphabet = array_merge(range('a', 'z'), range('A', 'Z'));
    
        foreach ($alphabet as $char) {
            if ($value = array_shift($fqEncWorkOgArrayUnique)) {
                $fqCharMap[$char] = $value;
                $fqEncString = str_replace($value, $char, $fqEncString);
            }
        }
    
        return ['string' => $fqEncString, 'map' => $fqCharMap];
    }
    
    // FucqEncodeAlgo Step 3
    private function encodeStep3(array $fqEncStringAndMap): string {
        $fqEncCharArray = explode(';', $fqEncStringAndMap['string']);
        $fqEncCharString = implode('', $fqEncCharArray);
    
        return bin2hex(gzencode($fqEncCharString. ';' . implode('/',$fqEncStringAndMap['map']),9));
    }
    
    // Main fucqEncodeAlgo method
    public function fucqEncodeAlgo(string $encodedString): string {
        $encodedString = $this->encode($encodedString);
        $step1Result = $this->encodeStep1($encodedString);
        $step2Result = $this->encodeStep2($step1Result);
    
        if (isset($step2Result['error'])) {
            return $step2Result['error'];  // Return error message if any
        }
    
        return $this->encodeStep3($step2Result);
    }
    
    public function fucqDecodeAlgoStep1(string $encodedString): array {
        $encodedString = gzdecode(hex2bin($encodedString));
        
        // Split the encoded string to get the encoded data and the character map
        list($encodedData, $encodedCharMap) = explode(';', $encodedString, 2);
    
        // Decode and decompress the encoded data
        $decodedData = $encodedData;
        if ($decodedData === false) {
            return ["error" => "Decompression or decoding of data failed"];
        }
    
        // Decode and decompress the character map
        $decodedCharMap = explode('/',$encodedCharMap);
        
        if ($decodedCharMap === false) {
            return ["error" => "Decompression or decoding of character map failed"];
        }
    
        return ["decodedData" => $decodedData, "decodedCharMap" => $decodedCharMap];
    }
    
    public function fucqDecodeAlgoStep2(array $decodedResults): string {
        if (isset($decodedResults['error'])) {
            return $decodedResults['error'];
        }
    
        $decodedData = $decodedResults['decodedData'];
        $decodedCharMap = $decodedResults['decodedCharMap'];
        
        $alphabet = array_merge(range('a', 'z'), range('A', 'Z'));
    
        // Reverse the process of mapping unique elements to alphabet characters
        foreach ($decodedCharMap as $char => $value) {
            if( !empty($value) )
            {
                $decodedData = str_replace($alphabet[$char], $value.';', $decodedData);
            }
        }
        
        // The result should be a string of concatenated unique elements
        return $decodedData;
    }

    public function fucqDecodeAlgoStep3(string $decodedData): string {
        // Split the string into segments
        $segments = explode(';', $decodedData);
        $originalString = '';
    
        foreach ($segments as $segment) {
            list($count, $char) = explode(',', $segment);
            $count = intval($count);  // Ensure count is an integer
            $originalString .= str_repeat($char, $count);
        }
    
        return $originalString;
    }


    /**
     * Decodes a string processed by fucqEncodeAlgo.
     * This method requires knowledge of the original characters or a way to infer them.
     * In its current state, it cannot fully reconstruct the original string without additional information.
     *
     * @param string $encodedString The fucqEncoded string to decode.
     * @return string The decoded string.
     */
    public function fucqDecodeAlgo(string $encodedString): string {
        // Step 1: Decode and decompress the encoded data and character map
        $step1Results = $this->fucqDecodeAlgoStep1($encodedString);
        if (isset($step1Results['error'])) {
            return $step1Results['error'];
        }
    
        // Step 2: Reverse the process of mapping unique elements to alphabet characters
        $decodedData = $this->fucqDecodeAlgoStep2($step1Results);
        if (is_string($decodedData) && strpos($decodedData, 'error') !== false) {
            return $decodedData; // Return error message if any
        }
    
        // Step 3: Reconstruct the original string from the decoded data
        return $this->decode($this->fucqDecodeAlgoStep3($decodedData));
    }



    // Additional methods and logic can be added here
}
