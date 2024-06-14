# Symfony Stripe Sandbox

Ce projet est une sandbox pour comprendre l'intégration de Stripe dans Symfony, avec un webhook pour déclencher des actions après un paiement.

## Prérequis

- **PHP 7.4 ou plus**
- **Composer**
- **Symfony CLI**
- **Stripe CLI**
- **Compte Stripe**

## Installation

1. Clone le repository :

   ```bash
   git clone https://github.com/PierreDenaes/symfony-stripe-sandbox.git
   cd symfony-stripe-sandbox
   ```

2. Clone le repository :

   ```bash
   composer install
    ```

3. Configure les variables d'environnement dans .env :

    ```bash
    STRIPE_SECRET_KEY=your_stripe_secret_key
    STRIPE_PUBLIC_KEY=your_stripe_public_key
    STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret
    ```

## Utilisation

### Lancer le serveur Symfony

```bash
symfony serve
```

### Créer un paiement Stripe

1. Accède à la page de paiement (ex. /payment).
2. Saisis les informations de la carte (utilise une carte de test Stripe).

### Webhook Stripe

1. Configure le webhook localement avec Stripe CLI :

    ```bash
    stripe listen --forward-to http://localhost:8000/webhook
    ```

2. Le webhook déclenche une action après le paiement, comme l'enregistrement dans une base de données.

## Structure du projet

- **/config** : Configuration de l'application.
- **/src** : Code source de l'application.
- **/templates** : Templates Twig pour le rendu des vues
- **/public** : Fichiers publics (CSS, JS, images).
- **/tests** : Tests unitaires et fonctionnels.

## Étapes importantes

1. Récupère tes clés API Stripe dans le tableau de bord Stripe.
2. Configure les clés dans le fichier **.env**

## Création de la session de paiement

Dans le contrôleur, crée une session de paiement :

```Php
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

public function createCheckoutSession()
{
    Stripe::setApiKey($this->getParameter('stripe_secret_key'));

    $session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Test Product',
                ],
                'unit_amount' => 2000,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
    ]);

    return $this->redirect($session->url, 303);
}
```

## Gestion du Webhook

1. Crée une route pour gérer le webhook :

```Php
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/webhook", name="webhook")
 */
public function handleWebhook(Request $request)
{
    $payload = @file_get_contents('php://input');
    $event = null;

    try {
        $event = \Stripe\Event::constructFrom(json_decode($payload, true));
    } catch(\UnexpectedValueException $e) {
        return new Response('', 400);
    }

    if ($event->type === 'checkout.session.completed') {
        // Logique après paiement
    }

    return new Response('', 200);
}
```

## Contribution

Les contributions sont les bienvenues ! Merci de soumettre une pull request.