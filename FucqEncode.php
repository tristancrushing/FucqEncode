<?php
ini_set('memory_limit', '8192M'); // For Testing Purposes, will lock down memeory requirments when I finalize algo.

/**
 * Class FucqEncode
 * Implements the 'Fucq' encoding and decoding algorithm, specifically optimized for JSON data.
 * 'Fucq' stands for "Frequently Used Character Quantification" encoding. 
 * This class offers methods for multi-layered bin2hex encoding with a unique stopping condition,
 * and a custom algorithm designed to make JSON data easily compressible and encodable, facilitating its transmission via GET requests.
 * 
 * The algorithm exhibits scalable compression, where its efficiency improves as the size of the data increases.
 * This characteristic makes it particularly effective for applications dealing with varying and large sizes of data.
 * 
 */
class FucqEncode {
    private int $maxLayers;

    /**
     * FucqEncode Constructor.
     * Initializes the FucqEncode object with specified maximum layers of encoding.
     * The class is optimized for encoding and decoding complex JSON structures,
     * making them suitable for efficient data transmission, such as via GET requests.
     *
     * @param int $maxLayers The maximum layers of encoding to apply. Defaults to 1.
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
     * This method is particularly effective for compressing JSON data.
     * It repetitively applies bin2hex encoding and stops when the encoded string
     * hits the $maxLayers ceiling, set to 1 by default, increasing this will further 
     * obsurificate the encoded string, but may decrase the compression efficiency
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

    /**
    * Encodes a given string by creating a sequence of counts of consecutive identical characters.
    * This is the first step in the Fucq encoding process, where the method transforms
    * the string into a series of character repetitions, laying the foundation for efficient compression,
    * especially beneficial for patterns found in JSON data.
    *
    * @param string $encodedString The string to be transformed.
    * @return array An array representing the sequence of character counts.
    */
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

    /**
    * Transforms an array of character counts into a string using a custom mapping logic.
    * This step maps unique elements of the transformed string to a set of alphabet characters,
    * creating a compact representation that further aids in compressing JSON data.
    *
    * @param array $fqEncWorkOgArray Array of original character counts.
    * @return array An array containing the transformed string and the character map used for the transformation.
    */
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
    
    /**
    * Finalizes the Fucq encoding process by compressing and encoding the character sequence.
    * This step applies gzencode and bin2hex to the transformed string, along with the character map,
    * producing a final string optimized for transmission and storage, particularly effective for JSON data.
    *
    * @param array $fqEncStringAndMap Array containing the transformed string and character map.
    * @return string The final compressed and encoded string ready for transmission.
    */
    private function encodeStep3(array $fqEncStringAndMap): string {
        $fqEncCharArray = explode(';', $fqEncStringAndMap['string']);
        $fqEncCharString = implode('', $fqEncCharArray);
    
        return bin2hex(gzencode($fqEncCharString. ';' . implode('/',$fqEncStringAndMap['map']),9));
    }
    
    /**
    * Main method to apply the Fucq encoding algorithm.
    * It orchestrates the process by sequentially calling the encoding steps, 
    * ensuring efficient compression and encoding of JSON data.
    *
    * @param string $encodedString The string to be encoded, typically a JSON structure.
    * @return string The encoded string, or an error message if the process fails.
    */
    public function fucqEncodeAlgo(string $encodedString): string {
        $encodedString = $this->encode($encodedString);
        $step1Result = $this->encodeStep1($encodedString);
        $step2Result = $this->encodeStep2($step1Result);
    
        if (isset($step2Result['error'])) {
            return $step2Result['error'];  // Return error message if any
        }
    
        return $this->encodeStep3($step2Result);
    }

    /**
    * First step in decoding a Fucq encoded string.
    * It decodes and decompresses the encoded data and character map, 
    * preparing them for the subsequent steps to reconstruct the original string.
    *
    * @param string $encodedString The Fucq encoded string to decode.
    * @return array An array containing the decoded data and character map.
    */
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

    /**
    * Second step in decoding a Fucq encoded string.
    * It reverses the mapping of unique elements to alphabet characters, 
    * reconstructing the transformed string to its original state before final decompression.
    *
    * @param array $decodedResults Array containing the decoded data and character map from the first step.
    * @return string The reconstructed string, ready for final decoding.
    */
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

    /**
    * Final step in decoding a Fucq encoded string.
    * It takes the reconstructed string from the previous step and replicates the characters 
    * based on their counts, effectively restoring the original string, 
    * ensuring the integrity of the original JSON data.
    *
    * @param string $decodedData The reconstructed string from the second step.
    * @return string The original string, fully decoded.
    */
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
    * Orchestrates the decoding process by calling the three decoding steps in sequence,
    * accurately reconstructing the original string, particularly effective for JSON structures.
    *
    * @param string $encodedString The fucqEncoded string to decode.
    * @return string The decoded string, or an error message if the process fails.
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
