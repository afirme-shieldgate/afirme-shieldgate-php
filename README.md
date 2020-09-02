# ShieldGate PHP SDK

## Language:: [EN](#Installation) || [ES](#Instalación)


### Installation

Install via composer (not hosted in packagist yet)

`composer require shieldgate/sdk`

## Usage

```php
<?php

require 'vendor/autoload.php';

use Payment\Payment;

// First setup your credentials provided by ShieldGate
$applicationCode = "SOME_APP_CODE";
$applicationKey = "SOME_APP_KEY";

Payment::init($applicationCode, $applicationKey);
```

Once time are set your credentials, you can use available resources.

Resources availables:

- **Card** 
 * Available methods: `getList`, `delete`
- **Charge**
 * Available methods: `create`, `authorize`, `capture`, `verify`, `refund`
- **Cash**
 * Available methods: `generateOrder`

### Card

See full documentation of these features [here](https://developers.shieldgate.mx/api/en/#payment-methods-cards).

#### List

```php
<?php

use Payment\Payment;
use Payment\Exceptions\PaymentErrorException;

Payment::init($applicationCode, $aplicationKey);

$card = Payment::card();

// Success response
$userId = "1";
$listOfUserCards = $card->getList($userId);

$totalSizeOfCardList = $listOfUserCards->result_size;
$listCards = $listOfUserCards->cards;

// Get all data of response
$response = $listOfUserCards->getData();

// Catch fail response
try {
	$listOfUserCards = $card->getList("someUID");
} catch (PaymentErrorException $error) {
	// Details of exception
	echo $error->getMessage();
	// You can see the logs for complete information
}
```

### Charges

See full documentation of these features [here](https://developers.shieldgate.mx/api/en/#payment-methods-cards).

#### Create new charge

See full documentation about this [here](https://developers.shieldgate.mx/api/en/#payment-methods-cards-debit-with-token)

```php
<?php

use Payment\Payment;
use Payment\Exceptions\PaymentErrorException;

// Card token
$cardToken = "myAwesomeTokenCard";

$charge = Payment::charge();

$userDetails = [
    'id' => "1", // Field required
    'email' => "dev@shieldgate.mx" // Field required
];

$orderDetails = [
    'amount' => 100.00, // Field required
    'description' => "XXXXXX", // Field required
    'dev_reference' => "XXXXXX", // Field required
    'vat' => 0.00 // Field required 
];

try {
    $created = $charge->create($cardToken, $orderDetails, $userDetails);
} catch (PaymentErrorException $error) {
    // See the console output for complete information
    // Access to HTTP code from gateway service
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Get transaction status
$status = $created->transaction->status;
// Get transaction ID
$transactionId = $created->transaction->id;
// Get authorization code
$authCode = $created->transaction->authorization_code;
```

#### Authorize charge

See the full documentation [here](https://developers.shieldgate.mx/api/en/#payment-methods-cards-authorize)

```php
<?php

use Payment\Payment;
use Payment\Exceptions\PaymentErrorException;

// Card token
$cardToken = "myAwesomeTokenCard";

$charge = Payment::charge();

$userDetails = [
    'id' => "1", // Field required
    'email' => "dev@shieldgate.mx" // Field required
];

$orderDetails = [
    'amount' => 100.00, // Field required
    'description' => "XXXXXX", // Field required
    'dev_reference' => "XXXXXX", // Field required
    'vat' => 0.00 // Field required 
];

try {
    $authorization = $charge->authorize($cardToken, $orderDetails, $userDetails);
} catch (PaymentErrorException $error) {
    // See the console output for complete information
    // Access to HTTP code from gateway service
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Get transaction status
$status = $authorization->transaction->status;
// Get transaction ID
$transactionId = $authorization->transaction->id;
// Get authorization code
$authCode = $authorization->transaction->authorization_code;
```

#### Capture

See the full documentation [here](https://developers.shieldgate.mx/api/en/#payment-methods-cards-capture)

Need make a [authorization process](#authorize-charge)

````php
<?php

use Payment\Payment;
use Payment\Exceptions\PaymentErrorException;

$charge = Payment::charge();

$authorization = $charge->authorize($cardToken, $orderDetails, $userDetails);
$transactionId = $authorization->transaction->id;

try {
    $capture = $charge->capture($transactionId);
} catch (PaymentErrorException $error) {
    // See the console output for complete information
    // Access to HTTP code from gwateway service
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Get transaction status
$status = $capture->transaction->status;

// Make a capture with different amount
$newAmountForCapture = 1000.46;
$capture = $charge->capture($transactionId, $newAmountForCapture);
````

#### Refund

See the full documentation [here](https://developers.shieldgate.mx/api/en/#payment-methods-cards-refund)

Need make a [create process](#authorize-charge)

````php
<?php

use Payment\Payment;
use Payment\Exceptions\PaymentErrorException;

$charge = Payment::charge();

$created = $charge->create($cardToken, $orderDetails, $userDetails);
$transactionId = $created->transaction->id;

try {
    $refund = $charge->refund($transactionId);
} catch (PaymentErrorException $error) {
    // See the console output for complete information
    // Access to HTTP code from gateway service
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Get status of refund
$status = $refund->status;
$detail = $refund->detail;

// Make a partial refund
$partialAmountToRefund = 10;
$refund = $charge->refund($transactionId, $partialAmountToRefund);
````

### Cash

#### Generate order

See the all available options in [here](https://developers.shieldgate.mx/api/en/#payment-methods-cash-generate-a-reference)

```php
<?php

use Payment\Payment;
use Payment\Exceptions\PaymentErrorException;

$cash = Payment::cash();

$carrierDetails = [
    'id' => 'oxxo', // Field required
    'extra_params' => [ // Depends of carrier, for oxxo is required
        'user' => [ // For oxxo is required
            'name' => "Juan",
            'last_name' => "Perez"
        ]
    ]
];

$userDetails = [
   'id' => "1", // Field required
   'email' => "randm@mail.com" // Field required
];

$orderDetails = [
    'dev_reference' => "XXXXXXX", // Field required 
    'amount' => 100, // Field required
    'expiration_days' => 1, // Field required
    'recurrent' => false, // Field required
    'description' => "XXXXXX" // Field required
];

try {
    $order = $cash->generateOrder($carrierDetails, 
    $userDetails, 
    $orderDetails);
} catch (PaymentErrorException $error) {
    // See the console output for complete information
    // Access to HTTP code from gateway service
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Get reference code
$referenceCode = $order->transaction->reference;
// Get expiration date
$expirationData = $order->transaction->expiration_date;
// Get order status
$status = $order->transaction->status;
```

### Run unit tests

`composer run test`

### =============================

### Instalación

Intalación via composer (Por ahora no disponible desde packagist)

`composer require shieldgate/sdk`

## Uso

```php
<?php

require 'vendor/autoload.php';

use Payment\Payment;

// Primero configura las credenciales otorgadas por ShieldGate
$applicationCode = "SOME_APP_CODE";
$applicationKey = "SOME_APP_KEY";

Payment::init($applicationCode, $applicationKey);
```

Una vez configuradas tus credenciales, puedes usar los recursos disponibles.

Recursos disponibles:

- **Card** 
 * Métodos disponibles: `getList`, `delete`
- **Charge**
 * Métodos disponibles: `create`, `authorize`, `capture`, `verify`, `refund`
- **Cash**
 * Métodos disponibles: `generateOrder`

### Tarjeta

Consulta la documentación completa de este medio de pago [aquí](https://developers.shieldgate.mx/api/#metodos-de-pago-tarjetas).

#### Listado

```php
<?php

use Payment\Payment;
use Payment\Exceptions\PaymentErrorException;

Payment::init($applicationCode, $aplicationKey);

$card = Payment::card();

// Respuesta exitosa
$userId = "1";
$listOfUserCards = $card->getList($userId);

$totalSizeOfCardList = $listOfUserCards->result_size;
$listCards = $listOfUserCards->cards;

// Obtener los datos de la respuesta
$response = $listOfUserCards->getData();

// Manejo de errores en la respuesta
try {
	$listOfUserCards = $card->getList("someUID");
} catch (PaymentErrorException $error) {
	// Detalles de la excepción
	echo $error->getMessage();
	// Puedes ver los logs para información completa
}
```

#### Cobro con token

Consulta la documentación completa sobre este servicio [aquí](https://developers.shieldgate.mx/api/#metodos-de-pago-tarjetas-cobro-con-token).

```php
<?php

use Payment\Payment;
use Payment\Exceptions\PaymentErrorException;

// Token de tarjeta
$cardToken = "myAwesomeTokenCard";

$charge = Payment::charge();

$userDetails = [
    'id' => "1", // Campo requerido
    'email' => "dev@shieldgate.mx" // Campo requerido
];

$orderDetails = [
    'amount' => 100.00, // Campo requerido
    'description' => "XXXXXX", // Campo requerido
    'dev_reference' => "XXXXXX", // Campo requerido
    'vat' => 0.00 // Campo requerido 
];

try {
    $created = $charge->create($cardToken, $orderDetails, $userDetails);
} catch (PaymentErrorException $error) {
    // Revisa la salida en consola para más información    
    // Acceso al código HTTP y el mensaje de error del servicio de la pasarela
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Obtiene el estado de la transacción
$status = $created->transaction->status;
// Obtiene el ID de la transacción
$transactionId = $created->transaction->id;
// Obtiene el código de autorización de la transacción
$authCode = $created->transaction->authorization_code;
```

#### Autorización

Consulta la documentación completa sobre este servicio [aquí](https://developers.shieldgate.mx/api/#metodos-de-pago-tarjetas-autorizar).

```php
<?php

use Payment\Payment;
use Payment\Exceptions\PaymentErrorException;

// Token de tarjeta
$cardToken = "myAwesomeTokenCard";

$charge = Payment::charge();

$userDetails = [
    'id' => "1", // Campo requerido
    'email' => "dev@shieldgate.mx" // Campo requerido
];

$orderDetails = [
    'amount' => 100.00, // Campo requerido
    'description' => "XXXXXX", // Campo requerido
    'dev_reference' => "XXXXXX", // Campo requerido
    'vat' => 0.00 // Campo requerido 
];

try {
    $authorization = $charge->authorize($cardToken, $orderDetails, $userDetails);
} catch (PaymentErrorException $error) {
    // Revisa la salida en consola para más información    
    // Acceso al código HTTP y el mensaje de error del servicio de la pasarela
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Obtiene el estado de la transacción
$status = $authorization->transaction->status;
// Obtiene el ID de la transacción
$transactionId = $authorization->transaction->id;
// Obtiene el código de autorización de la transacción
$authCode = $authorization->transaction->authorization_code;
```

#### Captura

Consulta la documentación completa sobre este servicio [aquí](https://developers.shieldgate.mx/api/#metodos-de-pago-tarjetas-captura).

Primero es requerida una [autorización](#autorización)

````php
<?php

use Payment\Payment;
use Payment\Exceptions\PaymentErrorException;

$charge = Payment::charge();

$authorization = $charge->authorize($cardToken, $orderDetails, $userDetails);
$transactionId = $authorization->transaction->id;

try {
    $capture = $charge->capture($transactionId);
} catch (PaymentErrorException $error) {
    // Revisa la salida en consola para más información    
    // Acceso al código HTTP y el mensaje de error del servicio de la pasarela
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Obtiene el estado de la transacción
$status = $capture->transaction->status;

// Realiza la captura con un monto diferente (Consulta con el equipo de integraciones sobre las limitaciones de cada operador)
$newAmountForCapture = 1000.46;
$capture = $charge->capture($transactionId, $newAmountForCapture);
````

#### Reembolso

Consulta la documentación completa sobre este servicio [aquí](https://developers.shieldgate.mx/api/#metodos-de-pago-tarjetas-reembolso).

Primero es requerida una [captura](#captura) o un [cobro](#cobro-con-token) 

````php
<?php

use Payment\Payment;
use Payment\Exceptions\PaymentErrorException;

$charge = Payment::charge();

$created = $charge->create($cardToken, $orderDetails, $userDetails);
$transactionId = $created->transaction->id;

try {
    $refund = $charge->refund($transactionId);
} catch (PaymentErrorException $error) {
    // Revisa la salida en consola para más información    
    // Acceso al código HTTP y el mensaje de error del servicio de la pasarela
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Obtiene el estado del reembolso
$status = $refund->status;
$detail = $refund->detail;

// Realiza un refund parcial (Consulta con el equipo de integraciones sobre las limitaciones de cada operador)
$partialAmountToRefund = 10;
$refund = $charge->refund($transactionId, $partialAmountToRefund);
````

### Efectivo

#### Generar una order

Consulta la documentación completa sobre este servicio [aquí](https://developers.shieldgate.mx/api/#metodos-de-pago-efectivo-generar-una-referencia).

```php
<?php

use Payment\Payment;
use Payment\Exceptions\PaymentErrorException;

$cash = Payment::cash();

$carrierDetails = [
    'id' => 'oxxo', // Campo requerido
    'extra_params' => [ // Depends of carrier, for oxxo is required
        'user' => [ // For oxxo is required
            'name' => "Juan",
            'last_name' => "Perez"
        ]
    ]
];

$userDetails = [
   'id' => "1", // Campo requerido
   'email' => "randm@mail.com" // Campo requerido
];

$orderDetails = [
    'dev_reference' => "XXXXXXX", // Campo requerido 
    'amount' => 100, // Campo requerido
    'expiration_days' => 1, // Campo requerido
    'recurrent' => false, // Campo requerido
    'description' => "XXXXXX" // Campo requerido
];

try {
    $order = $cash->generateOrder($carrierDetails, 
    $userDetails, 
    $orderDetails);
} catch (PaymentErrorException $error) {
    // Revisa la salida en consola para más información    
    // Acceso al código HTTP y el mensaje de error del servicio de la pasarela
    $code = $error->getCode();
    $message = $error->getMessage();
}

// Obtiene el código / referencia de pago
$referenceCode = $order->transaction->reference;
// Obtiene la fecha de expiración
$expirationData = $order->transaction->expiration_date;
// Obtiene el estado de la orden
$status = $order->transaction->status;
```

### Ejecutar pruebas unitarias

`composer run test`
