<?php

/*
|--------------------------------------------------------------------------
| CORE LTD EMAIL CONFIGURATION
|--------------------------------------------------------------------------
|
| LOCALHOST TESTING
|
| Later, when the website moves to cPanel, we will replace these
| settings with the official CORE LTD domain email / SMTP details.
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| SENDER
|--------------------------------------------------------------------------
|
| Enter the exact email address you want to use for testing.
|
*/

$MAIL_FROM_EMAIL = 'info@coreltd.co.ke';

$MAIL_FROM_NAME = 'Alex Gitahi - CORE LTD';


/*
|--------------------------------------------------------------------------
| CORE LTD SHOP EMAIL
|--------------------------------------------------------------------------
|
| Every successful order will be sent here.
|
*/

$SHOP_EMAIL = 'corecyber254@gmail.com';


/*
|--------------------------------------------------------------------------
| SEND ORDER EMAIL
|--------------------------------------------------------------------------
*/

function sendOrderEmails(
    $orderNumber,
    $customerName,
    $customerPhone,
    $customerEmail,
    $town,
    $deliveryAddress,
    $orderNotes,
    $items,
    $subtotal,
    $deliveryFee,
    $totalAmount
) {

    global $MAIL_FROM_EMAIL;
    global $MAIL_FROM_NAME;
    global $SHOP_EMAIL;


    /*
    |--------------------------------------------------------------------------
    | BUILD ORDER ITEMS HTML
    |--------------------------------------------------------------------------
    */

    $itemsHtml = '';

    foreach ($items as $item) {

        $name = htmlspecialchars(
            $item['product_name']
        );

        $quantity = (int)$item['quantity'];

        $unitPrice = number_format(
            (float)$item['unit_price'],
            2
        );

        $itemTotal = number_format(
            (float)$item['subtotal'],
            2
        );


        $itemsHtml .= "

        <tr>

            <td style='padding:12px;border-bottom:1px solid #eee;'>

                {$name}

            </td>

            <td style='padding:12px;border-bottom:1px solid #eee;text-align:center;'>

                {$quantity}

            </td>

            <td style='padding:12px;border-bottom:1px solid #eee;text-align:right;'>

                Ksh. {$unitPrice}

            </td>

            <td style='padding:12px;border-bottom:1px solid #eee;text-align:right;'>

                Ksh. {$itemTotal}

            </td>

        </tr>

        ";
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER EMAIL
    |--------------------------------------------------------------------------
    */

    $customerSubject =
        "CORE LTD - Order {$orderNumber} Received";


    $customerBody = "

    <!DOCTYPE html>

    <html>

    <body style='margin:0;padding:0;background:#f5f5f5;font-family:Arial,sans-serif;'>

        <div style='max-width:700px;margin:30px auto;background:#ffffff;'>

            <div style='background:#111111;color:#ffffff;padding:25px;text-align:center;'>

                <h1 style='margin:0;'>
                    CORE LTD
                </h1>

                <p style='margin:8px 0 0;'>
                    Networking • Internet • CCTV • Fiber
                </p>

            </div>


            <div style='padding:30px;'>

                <h2>
                    Order Received
                </h2>

                <p>
                    Hello <strong>"
        . htmlspecialchars($customerName)
        . "</strong>,
                </p>

                <p>
                    Thank you for your order with CORE LTD.
                    Your order has been received successfully.
                </p>


                <div style='background:#f7f7f7;padding:18px;margin:20px 0;'>

                    <strong>
                        Order Number:
                    </strong>

                    {$orderNumber}

                    <br><br>

                    <strong>
                        Order Total:
                    </strong>

                    Ksh. "
        . number_format($totalAmount, 2)
        . "

                </div>


                <h3>
                    Your Order
                </h3>


                <table
                    width='100%'
                    cellspacing='0'
                    cellpadding='0'
                    style='border-collapse:collapse;'
                >

                    <thead>

                        <tr style='background:#f1f1f1;'>

                            <th style='padding:12px;text-align:left;'>
                                Product
                            </th>

                            <th style='padding:12px;text-align:center;'>
                                Qty
                            </th>

                            <th style='padding:12px;text-align:right;'>
                                Unit Price
                            </th>

                            <th style='padding:12px;text-align:right;'>
                                Total
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        {$itemsHtml}

                    </tbody>

                </table>


                <div style='margin-top:25px;'>

                    <p>
                        <strong>
                            Subtotal:
                        </strong>

                        Ksh. "
        . number_format($subtotal, 2)
        . "
                    </p>

                    <p>
                        <strong>
                            Delivery:
                        </strong>

                        Ksh. "
        . number_format($deliveryFee, 2)
        . "
                    </p>

                    <p style='font-size:18px;'>

                        <strong>
                            Total:
                        </strong>

                        Ksh. "
        . number_format($totalAmount, 2)
        . "

                    </p>

                </div>


                <div style='background:#fff8e1;padding:20px;margin-top:25px;'>

                    <h3>
                        What Happens Next?
                    </h3>

                    <p>

                        A CORE LTD sales representative will
                        contact you to confirm your order and
                        provide payment instructions.

                    </p>

                    <p>

                        <strong>
                            Please do not make payment until
                            your order has been confirmed by
                            a CORE LTD representative.
                        </strong>

                    </p>

                </div>


                <p style='margin-top:30px;'>

                    Thank you for choosing
                    <strong>CORE LTD</strong>.

                </p>

            </div>


            <div style='background:#111111;color:#ffffff;padding:20px;text-align:center;'>

                CORE LTD

                <br>

                Networking • Internet • CCTV • Fiber

            </div>

        </div>

    </body>

    </html>

    ";


    /*
    |--------------------------------------------------------------------------
    | COMPANY EMAIL
    |--------------------------------------------------------------------------
    */

    $companySubject =
        "NEW CORE LTD ORDER - {$orderNumber}";


    $companyBody = "

    <!DOCTYPE html>

    <html>

    <body style='margin:0;padding:0;background:#f5f5f5;font-family:Arial,sans-serif;'>

        <div style='max-width:800px;margin:30px auto;background:#ffffff;'>

            <div style='background:#111111;color:#ffffff;padding:25px;'>

                <h1 style='margin:0;'>
                    CORE LTD
                </h1>

                <p>
                    NEW WEBSITE ORDER
                </p>

            </div>


            <div style='padding:30px;'>

                <h2>
                    New Order Received
                </h2>


                <div style='background:#f7f7f7;padding:20px;'>

                    <strong>
                        Order Number:
                    </strong>

                    {$orderNumber}

                    <br><br>

                    <strong>
                        Customer:
                    </strong>

                    "
        . htmlspecialchars($customerName)
        . "

                    <br>

                    <strong>
                        Phone:
                    </strong>

                    "
        . htmlspecialchars($customerPhone)
        . "

                    <br>

                    <strong>
                        Email:
                    </strong>

                    "
        . htmlspecialchars(
            $customerEmail ?: 'Not provided'
        )
        . "

                    <br>

                    <strong>
                        Town / Area:
                    </strong>

                    "
        . htmlspecialchars($town)
        . "

                    <br>

                    <strong>
                        Delivery Address:
                    </strong>

                    "
        . htmlspecialchars($deliveryAddress)
        . "

                </div>


                <h3 style='margin-top:30px;'>
                    Ordered Products
                </h3>


                <table
                    width='100%'
                    cellspacing='0'
                    cellpadding='0'
                    style='border-collapse:collapse;'
                >

                    <thead>

                        <tr style='background:#f1f1f1;'>

                            <th style='padding:12px;text-align:left;'>
                                Product
                            </th>

                            <th style='padding:12px;text-align:center;'>
                                Qty
                            </th>

                            <th style='padding:12px;text-align:right;'>
                                Unit Price
                            </th>

                            <th style='padding:12px;text-align:right;'>
                                Total
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        {$itemsHtml}

                    </tbody>

                </table>


                <div style='margin-top:25px;'>

                    <p>
                        Subtotal:
                        <strong>
                            Ksh. "
        . number_format($subtotal, 2)
        . "
                        </strong>
                    </p>

                    <p>
                        Delivery:
                        <strong>
                            Ksh. "
        . number_format($deliveryFee, 2)
        . "
                        </strong>
                    </p>

                    <p style='font-size:20px;'>

                        <strong>
                            TOTAL:
                            Ksh. "
        . number_format($totalAmount, 2)
        . "
                        </strong>

                    </p>

                </div>


                <div style='background:#fff8e1;padding:20px;margin-top:25px;'>

                    <strong>
                        Payment Status:
                    </strong>

                    Pending

                    <br><br>

                    <strong>
                        Order Status:
                    </strong>

                    Pending

                </div>


                <h3>
                    Customer Notes
                </h3>

                <p>

                    "
        . nl2br(
            htmlspecialchars(
                $orderNotes ?: 'No additional notes.'
            )
        )
        . "

                </p>

            </div>

        </div>

    </body>

    </html>

    ";


    /*
    |--------------------------------------------------------------------------
    | EMAIL HEADERS
    |--------------------------------------------------------------------------
    */

    $headers = [];

    $headers[] =
        "MIME-Version: 1.0";

    $headers[] =
        "Content-Type: text/html; charset=UTF-8";

    $headers[] =
        "From: "
        . $MAIL_FROM_NAME
        . " <"
        . $MAIL_FROM_EMAIL
        . ">";


    $headersString =
        implode(
            "\r\n",
            $headers
        );


    /*
    |--------------------------------------------------------------------------
    | SEND TO COMPANY
    |--------------------------------------------------------------------------
    */

    $companySent =
        mail(
            $SHOP_EMAIL,
            $companySubject,
            $companyBody,
            $headersString
        );


    /*
    |--------------------------------------------------------------------------
    | SEND TO CUSTOMER
    |--------------------------------------------------------------------------
    |
    | Only send if customer provided an email.
    |
    */

    $customerSent = true;


    if (
        !empty($customerEmail)
        &&
        filter_var(
            $customerEmail,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $customerSent =
            mail(
                $customerEmail,
                $customerSubject,
                $customerBody,
                $headersString
            );
    }


    /*
    |--------------------------------------------------------------------------
    | RETURN RESULT
    |--------------------------------------------------------------------------
    */

    return [

        'company_sent' =>
        $companySent,

        'customer_sent' =>
        $customerSent

    ];
}
