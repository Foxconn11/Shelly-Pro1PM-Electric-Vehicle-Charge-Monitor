# Shelly Pro1PM Electric Vehicle Charge Monitor

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-blue)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-Custom-lightgrey)](LICENSE)
[![GitHub Repo](https://img.shields.io/badge/GitHub-Foxconn11-blue?logo=github)](https://github.com/Foxconn11/Shelly-Pro1PM-Electric-Vehicle-Charge-Monitor)

**Shelly Pro1PM Electric Vehicle Charge Monitor** is a web-based dashboard and logging tool for monitoring EV charging with a **Shelly Pro 1 PM**. It records charged kWh, calculates consumption, and displays live metrics from your Shelly device.

---

## Features

- **Live Data Display** (updates every second)
  - Current charged kWh
  - Charged kWh since last entry
  - Current power (W)
  - Grid voltage (V)
  - Current (A)
  - Frequency (Hz)

- **Optional Expanded Details** (click to show)
  - Shelly internal temperature (°C)
  - Power factor
  - Switch state (ON/OFF)
  - WiFi signal strength (RSSI)
  - Cloud, MQTT, and WebSocket connection status
  - Uptime, free RAM, and free flash memory

- **Database Logging**
  - Enter your car´s odometer (km) and log kWh from Shelly
  - Calculates charged kWh since last entry
  - Computes consumption (kWh per 100 km)
  - Stored in a MySQL table (`ladehistorie`) for historical analysis

- **Auto-updating Dashboard**
  - Live values refresh every second
  - Fully responsive design with dark theme and white Arial font

- **Easy Installation**
  - `install.php` sets up the database table automatically
  - Works with existing MySQL databases
  - Simple configuration for your Shelly device IP

---


## Setup Instructions

1. **Clone the repository**

```bash
git clone https://github.com/Foxconn11/Shelly-Pro1PM-Electric-Vehicle-Charge-Monitor
```

2. **Start the Installer Script in your Webbrowser**:

```
http://your-server/install.php
```

3. **Fill out the Credentials to your MySQL Database and ShellyIP**

4. **Click on Run Installation**:

5. **Done**


---

## Usage

1. Enter the **current odometer reading** in km.
2. Click **“Shelly-KWh abrufen & speichern”** to fetch the latest Shelly kWh and calculate charged energy and consumption.
3. See live metrics updating every second.
4. Optionally expand the **“Weitere Details anzeigen”** section to monitor device status.

---

## Planned Changes

- [ ] Add Language Support
- [x] Improve the installation script
- [ ] Add the ability to switch to miles
- [x] Add a central configuration file for:
    - Database connection settings
    - Shelly device IP
    - Preferred units (km/miles, kWh/Wh)
    - Update intervals
- [ ] Add Settings Page

---

## Current known Issues to resolve

none

---

## Requirements

- PHP 7.4+
- MySQL or MariaDB
- Shelly Pro 1 PM on the same network
- Web server (Apache, Nginx, etc.)

---

## License

See the `LICENSE` file for details.



