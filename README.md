[![LOGO](https://github.com/irivengroup/assets/blob/main/img/fluxon.png)](https://github.com/irivengroup/fluxon)

<p align="center">
  <strong>PHP Form Engine. Limitless Possibilities.</strong>
  A modern, extensible, and production-ready PHP form engine for building forms, APIs, and distributed systems.
</p>

<p align="center">
  <a href="https://packagist.org/packages/iriven/fluxon"><img src="https://img.shields.io/packagist/v/iriven/fluxon" alt="Latest Version"></a>
  <a href="https://packagist.org/packages/iriven/fluxon"><img src="https://img.shields.io/packagist/dt/iriven/fluxon" alt="Downloads"></a>
  <a href="#"><img src="https://img.shields.io/badge/PHP-8.2%2B-blue" alt="PHP Version"></a>
  <a href="#"><img src="https://img.shields.io/badge/status-stable-brightgreen" alt="Status"></a>
  <a href="#"><img src="https://img.shields.io/badge/license-MIT-green" alt="License"></a>
</p>

---

## 🚀 What is Fluxon?

**Fluxon** is a next-generation **PHP Form Engine** designed for modern architectures:

* Headless applications
* API-first systems
* Microservices & distributed environments
* Frontend-driven UIs (React, Vue, etc.)

It allows you to **define forms once and use them everywhere**.

---

## ✨ Key Features

### 🧱 Form Definition Engine

* Define forms using PHP arrays, DTOs, or objects
* Automatic schema generation
* Built-in validation support

### 🎯 Frontend SDK (JSON-first)

* Unified schema output
* UI component abstraction
* Multi-channel rendering

### ⚙️ Runtime Engine

* Synchronous execution
* Context-aware processing
* Fully decoupled architecture

### 🧵 Async Processing (Queue-ready)

* Job dispatching
* Serializable execution context
* Queue transport abstraction (Redis/SQS-ready)

### 🔌 Plugin Ecosystem (V7+)

* Official plugins (CSRF, Captcha, Upload, etc.)
* Custom plugin support
* Stable plugin contracts

### 📡 Headless Mode

* Pure validation engine
* API-ready responses
* No UI dependency

### 📊 Observability & Diagnostics

* Runtime profiling
* Production diagnostics
* Debug CLI tools

---

## 📦 Installation

```bash
composer require iriven/fluxon
```

---

## ⚡ Quick Start

### Define a form

```php
use Iriven\Fluxon\Domain\Form\Form;

$form = new Form('contact', [
    'email' => ['type' => 'email'],
    'message' => ['type' => 'textarea'],
]);
```

---

### Generate frontend schema

```php
use Iriven\Fluxon\Application\Frontend\FrontendSdk;

$sdk = new FrontendSdk();
$schema = $sdk->build($form);
```

---

### Headless validation

```php
use Iriven\Fluxon\Application\Headless\HeadlessFormProcessor;

$processor = new HeadlessFormProcessor();

$result = $processor->submit($form, [
    'email' => 'john@example.com',
    'message' => 'Hello'
]);
```

---

### Async dispatch

```php
use Iriven\Fluxon\Application\Runtime\AsyncRuntimeDispatcher;

$dispatcher = new AsyncRuntimeDispatcher();

$result = $dispatcher->dispatch(
    'submit',
    'contact',
    ['email' => 'john@example.com']
);
```

---

## 🧵 Async Example Output

```json
{
  "transport": "queue",
  "status": "queued",
  "queue_size": 1
}
```

---

## 🔌 Plugin System

```php
interface OfficialPluginInterface
{
    public function name(): string;
    public function version(): string;
    public function register(PluginContext $context): void;
}
```

### Official Plugins

* CSRF Protection
* Captcha Integration
* Media Upload
* Audit Trail
* Async Dispatch Enhancements

---

## 🧠 Architecture

```
src/
├── Application/
│   ├── Runtime/
│   ├── Headless/
│   ├── Frontend/
│   ├── Profiling/
│   ├── Diagnostics/
│   ├── Ecosystem/
│   └── Sdk/
├── Domain/
├── Infrastructure/
└── Integration/
```

---

## 🛠 CLI

```bash
php bin/console debug:schema
php bin/console debug:profile
php bin/console debug:diagnostics
php bin/console plugin:list
php bin/console ecosystem:report
```

---

## 📊 Quality

### Tests

```bash
vendor/bin/phpunit
```

### Static Analysis

```bash
vendor/bin/phpstan analyse src tests
```

---

## 📚 Documentation

* Async Runtime
* Queue Transport
* Distributed Execution
* Plugin Development
* Advanced SDKs

---

## 🔄 Versioning

Fluxon follows **Semantic Versioning**:

* **Major** → architecture changes
* **Minor** → new features
* **Patch** → fixes and stability

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests
4. Submit a Pull Request

---

## 📜 License

MIT

---

## 🌍 Vision

Fluxon is a **schema-driven platform** for building:

* dynamic UIs
* API-driven applications
* distributed systems

---

## 🧩 Roadmap

* JavaScript SDK (React/Vue)
* Plugin marketplace
* Redis / SQS transport
* Visual form builder
* Distributed runtime engine

---

## ⭐ Why Fluxon?

* Clean architecture
* Headless-first design
* Async-ready
* Plugin ecosystem
* Production-grade tooling

---

## 💡 Signature

> Fluxon — PHP Form Engine. Limitless Possibilities.
