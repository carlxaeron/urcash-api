<?php

namespace App\Http\Helper\Utils;

use App\Price;
use App\Purchase;
use App\PurchaseItem;
use App\Shop;
use App\VerificationCode;
use App\Http\Helper\Debugging\GetSqlWithBindings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Image;


class Helper
{
    public function generateCode($data = []) {
        $digits = $data['num'] ?? 4;
        $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);

        return $otp;
    }
}

/**
 * Class RandomStringGenerator
 * @package Utils
 *
 */
class RandomStringGenerator
{
    /** @var string */
    protected $alphabet;
    /** @var int */
    protected $alphabetLength;

    /**
     * @param string $alphabet
     */
    public function __construct($alphabet = '')
    {
        if ('' !== $alphabet) {
            $this->setAlphabet($alphabet);
        } else {
            $this->setAlphabet(
                implode(range('A', 'Z'))
                . implode(range(0, 9))
            );
        }
    }

    /**
     * @param string $alphabet
     */
    public function setAlphabet($alphabet)
    {
        $this->alphabet = $alphabet;
        $this->alphabetLength = strlen($alphabet);
    }

    /**
     * @param int $length
     * @return string
     */
    public function generate($length)
    {
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $randomKey = $this->getRandomInteger(0, $this->alphabetLength);
            $token .= $this->alphabet[$randomKey];
        }

        return $token;
    }

    /**
     * @param int $min
     * @param int $max
     * @return int
     */
    protected function getRandomInteger($min, $max)
    {
        $range = ($max - $min);

        if ($range < 0) {
            return $min; // Not so random
        }

        $log = log($range, 2);

        $bytes = (int) ($log / 8) + 1; // Length in bytes

        $bits = (int) $log + 1; // Length in bits

        $filter = (int) (1 << $bits) - 1; // Set all lower bits to 1

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));

            $rnd = $rnd & $filter; // Discard irrelevant bits
        } while ($rnd >= $range);

        return ($min + $rnd);
    }
}

/**
 * Class CalculateSales
 * @package Utils
 *
 */
class CalculateSales
{
    // Global variables
    public $total_sales = 0;
    public $subtotal = 0;
    public $merchants = array();
    public $calculated_sales = array();

    /**
     * @return array
     */
    public function calculate($address, $address_field, $address_type_string, $from, $to) {
        $from = $from. " 00:00:00";
        $to = $to. " 23:59:59";
        for ($i = 0; $i < $address->count(); $i++) {
            $shop = Shop::where('address_id', '=', $address[$i]['id'])->first();

            if ($shop) {
                $find_transactions = Purchase::where('shop_id', '=', $shop->id)->get();

                if ($find_transactions->count() > 1) { // Check if merchant has purchase transactions
                    for ($j = 0; $j < $find_transactions->count(); $j++) {
                        $get_purchased_items = PurchaseItem::where('purchase_id', '=', $find_transactions[$j]['id'])
                            ->whereBetween('created_at', [$from, $to])->get();
                        $this->subtotal = $this->calculateSubtotal($get_purchased_items, $shop->id);
                        $this->merchants[$i]['total_earnings'] = $this->subtotal;
                    }
                    $this->total_sales += $this->merchants[$i]['total_earnings'];

                } else { // Else set total earnings as zero
                    $this->merchants[$i]['total_earnings'] = 0;
                }
                $this->merchants[$i]['shop_id'] = $shop->id;
                $this->merchants[$i][$address_type_string] = $address_field;
                $this->merchants[$i]['merchant_name'] = $shop->reg_bus_name;
            }
            $this->subtotal = 0; // Reset for calculating total earnings of next merchant
        }
        $this->calculated_sales['merchants'] = $this->merchants;
        $this->calculated_sales['total_sales'] = $this->total_sales;

        return $this->calculated_sales;
    }

    /**
     * $get_purchased_items = PurchaseItem instance where ('purchase_id', '=', $purchase_id)->get();
     * @return float
     */
    public function calculateSubtotal($get_purchased_items, $shop_id) {
        for ($k = 0; $k < $get_purchased_items->count(); $k++) {
            $get_price = Price::where('product_id', '=', $get_purchased_items[$k]->product_id)
                ->where('shop_id', '=', $shop_id)->first()->price;
            $calculate_prices = $get_purchased_items[$k]['quantity'] * $get_price;
            $this->subtotal += $calculate_prices;
        }
        return round($this->subtotal, 2); // Round off to 2 decimal places
    }

    /**
     * $get_purchased_items = PurchaseItem instance where ('product_id', '=', $product_id)->get();
     * @return float
     */
    public function calculateSubtotalForPurchaseItemProducts($product_id, $shop_id, $quantity) {
        $get_price = Price::where('product_id', '=', $product_id)
            ->where('shop_id', '=', $shop_id)->first()->price;
        $subtotal = $quantity * $get_price;

        return round($subtotal, 2); // Round off to 2 decimal places
    }
}

/**
 * Class GenerateRandomIntegers
 * @package Utils
 *
 */
class GenerateRandomIntegers
{
    // Global variables
    public $int_start;
    public $int_end;
    public $length;
    public $generated_codes_list = array();

    public function __construct($int_start, $int_end, $length) {
        $this->int_start = (int) $int_start;
        $this->int_end = (int) $int_end;
        $this->length = (int) $length;
    }

    /**
     * @return int
     */
    public function generate() {
        $code = '';

        // Generate random integers between 1 - 9
        for ($i = 0; $i < $this->length; $i++) {
            $code .= rand($this->int_start, $this->int_end);
        }

        // Check if verification code already exists in the verification_codes table
        $find_verification_code = VerificationCode::where('code', '=', $code)->first();

        // Debugging while loop counters and testing. Please do not remove this yet.
        // $this->generated_codes($code);
        $new_code = '';

        // If code exists, generate a new one.
        if ($find_verification_code) {
            $will_generate = True;

            while ($will_generate == True) { // Loop while this condition is met
                $new_code = ''; // Reset value before concatenating
                // Generate another code using random integers
                for ($i = 0; $i < $this->length; $i++) {
                    $new_code .= rand($this->int_start, $this->int_end);
                }
                $find_verification_code_if_exists = VerificationCode::where('code', '=', $new_code)->first();

                // If verification code does not exist, exit the while loop
                if (!$find_verification_code_if_exists) {
                    $will_generate = False;
                }
                // $this->generated_codes($new_code); // Push generated codes into array for debugging
            }
            $code = $new_code; // Use newly generated code as final code
        }
        return $code;
    }

    public function generated_codes($code_to_be_pushed) {
        array_push($this->generated_codes_list, $code_to_be_pushed);

        return $this->generated_codes_list;
    }
}

/**
 * Class GetTransactions
 * @package Utils
 *
 */
class GetTransactions
{
    // Global variables
    public $transactions = array();
    public $total_sales = 0;

    /**
     * @return float
     */
    public function getTransactions($get_purchases) {
        for ($i = 0; $i < $get_purchases->count(); $i++) {
            $shop = Shop::where('id', '=', $get_purchases[$i]['shop_id'])->first();
            if ($shop) {
                $get_purchased_items = PurchaseItem::where('purchase_id', '=', $get_purchases[$i]['id'])->get();

                $calculate_sales = new CalculateSales();
                $this->total_sales += $calculate_sales->calculateSubtotal($get_purchased_items, $shop->id);
            }
        }
        return $this->total_sales;
    }
}

/**
 * Class SortAndSliceArrayByKey
 * @package Utils
 *
 */
class SortAndSliceArrayByKey
{
    // Global variables
    public $array;
    public $array_key;
    public $length_to_slice;
    public $sliced_array = array();

    public function __construct($array, $array_key, $length_to_slice) {
        $this->array = $array;
        $this->array_key = $array_key;
        $this->length_to_slice = $length_to_slice;
    }

    /**
     * @return array
     */
    public function sortAndSlice() {
        usort($this->array, function ($a, $b) { // Sort indices by key
            return $b[$this->array_key] - $a[$this->array_key];
        });
        // Slice array based on value of $length_to_slice
        $this->sliced_array = array_slice($this->array, 0, $this->length_to_slice);
        return $this->sliced_array;
    }
}

/**
 * Class UploadImage
 * @package Utils
 *
 */
class UploadImage
{
    // Global variables
    public $request;
    public $field_name;
    public $file_name;
    public $upload_to_directory;

    public function __construct(Request $request, $field_name, $file_name, $upload_to_directory) {
        $this->request = $request;
        $this->field_name = $field_name;
        $this->file_name = $file_name;
        $this->upload_to_directory = $upload_to_directory;
    }

    /**
     * @return string
     */
    public function upload() {
        if (!File::isDirectory(public_path($this->upload_to_directory))) { // If directory does not exist, create it
            File::makeDirectory(public_path($this->upload_to_directory), 0777, true, true);
        }

        $image = $this->request->file($this->field_name); // Get uploaded file
        $image_filename = $this->file_name . '.' . $image->getClientOriginalExtension(); // Generate filename
        $resize_image = Image::make($image->getRealPath()); // Resize image

        if ($resize_image->width() > 720) { // Resize image if image is greater than 720 pixels wide
            $resize_image->resize(720, 720, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $string_without_slash_start = Str::startsWith($this->upload_to_directory, '/');
        $string_without_slash_end = Str::endsWith($this->upload_to_directory, '/');

        if (!$string_without_slash_start) { // If $upload_to_directory string does not start with a slash, append it
            $this->upload_to_directory = '/' . $this->upload_to_directory;
        }

        if (!$string_without_slash_end) { // If $upload_to_directory string does not end with a slash, append it
            $this->upload_to_directory = $this->upload_to_directory . '/';
        }

        // Upload image to value of $upload_to_directory
        // eg if $upload_to_directory = '/images/uploads/products/'
        // then it will be uploaded to public/images/uploads/products/ directory
        $resize_image->save(public_path($this->upload_to_directory) . $image_filename);

        $string = Str::of($this->upload_to_directory)
            ->ltrim('/')
            ->replaceMatches('/[^\w-]++/', '\\\\\\\\');
        $transform_string = (string) Str::lower($string);
        $file_path = $transform_string . $image_filename;

        return $file_path;
    }
}
