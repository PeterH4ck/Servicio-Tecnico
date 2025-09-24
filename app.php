<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ESP32 Controller - SKYN3T</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #000000;
            color: #ffffff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* Fondo espacial animado */
        .space-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            background: radial-gradient(ellipse at center, #0a0e27 0%, #000000 100%);
        }

        /* Estrellas animadas */
        .stars {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .star {
            position: absolute;
            width: 2px;
            height: 2px;
            background: white;
            border-radius: 50%;
            animation: twinkle 3s infinite;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0; transform: scale(0.5); }
            50% { opacity: 1; transform: scale(1); }
        }

        /* Estado de conexión */
        .connection-status {
            margin-top: 40px;
            text-align: center;
            z-index: 10;
        }

        .status-text {
            font-size: 18px;
            font-weight: 600;
            color: rgba(255, 68, 68, 0.9);
            transition: all 0.3s ease;
            text-shadow: 0 0 20px currentColor;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .status-text.connected {
            color: rgba(68, 255, 68, 0.9);
        }

        .status-text.connecting {
            color: rgba(255, 170, 0, 0.9);
        }

        .device-name {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 8px;
            letter-spacing: 1px;
        }

        /* Contenedor principal */
        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            position: relative;
        }

        /* Botón principal ON/OFF - Glassmorphism */
        .main-button-container {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .main-button {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 0 20px rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .main-button::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 40%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .main-button:hover {
            transform: scale(1.05);
            box-shadow: 
                0 15px 40px rgba(0, 0, 0, 0.4),
                inset 0 0 30px rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .main-button:hover::before {
            opacity: 1;
        }

        .main-button:active {
            transform: scale(0.95);
        }

        .main-button.on {
            background: rgba(68, 255, 68, 0.1);
            border: 1px solid rgba(68, 255, 68, 0.3);
            box-shadow: 
                0 8px 32px rgba(68, 255, 68, 0.2),
                inset 0 0 20px rgba(68, 255, 68, 0.1),
                0 0 60px rgba(68, 255, 68, 0.2);
        }

        .main-button-text {
            font-size: 48px;
            font-weight: 300;
            color: rgba(255, 68, 68, 0.9);
            transition: all 0.3s ease;
            text-shadow: 0 0 20px currentColor;
            letter-spacing: 3px;
        }

        .main-button.on .main-button-text {
            color: rgba(68, 255, 68, 0.9);
            font-weight: 400;
        }

        /* Botón secundario START - Glassmorphism */
        .secondary-button-container {
            position: absolute;
            top: 75%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .secondary-button {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 
                0 4px 20px rgba(0, 0, 0, 0.2),
                inset 0 0 15px rgba(255, 255, 255, 0.02);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: not-allowed;
            opacity: 0.3;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        .secondary-button.enabled {
            cursor: pointer;
            opacity: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .secondary-button.enabled:hover {
            transform: scale(1.1);
            box-shadow: 
                0 8px 25px rgba(0, 0, 0, 0.3),
                inset 0 0 20px rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .secondary-button.enabled:active {
            transform: scale(0.95);
        }

        .secondary-button.active {
            background: rgba(68, 255, 68, 0.1);
            border: 1px solid rgba(68, 255, 68, 0.3);
            box-shadow: 
                0 4px 20px rgba(68, 255, 68, 0.2),
                inset 0 0 15px rgba(68, 255, 68, 0.1),
                0 0 40px rgba(68, 255, 68, 0.15);
        }

        .secondary-button-text {
            font-size: 16px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            text-shadow: 0 0 10px currentColor;
            letter-spacing: 1px;
        }

        .secondary-button.enabled .secondary-button-text {
            color: rgba(255, 255, 255, 0.8);
        }

        .secondary-button.active .secondary-button-text {
            color: rgba(68, 255, 68, 0.9);
        }

        .timer-text {
            font-size: 20px;
            color: rgba(68, 255, 68, 0.9);
            margin-top: 4px;
            font-weight: 600;
            text-shadow: 0 0 15px currentColor;
        }

        /* Botón de conexión - Glassmorphism */
        .connect-button-container {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
        }

        .connect-button {
            padding: 14px 40px;
            background: rgba(0, 102, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 102, 255, 0.3);
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 
                0 4px 20px rgba(0, 102, 255, 0.2),
                inset 0 0 20px rgba(0, 102, 255, 0.05);
        }

        .connect-button:hover {
            background: rgba(0, 102, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 
                0 8px 30px rgba(0, 102, 255, 0.3),
                inset 0 0 25px rgba(0, 102, 255, 0.1);
            border: 1px solid rgba(0, 102, 255, 0.4);
        }

        .connect-button:active {
            transform: translateY(0);
        }

        .connect-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Toast notifications */
        .toast {
            position: fixed;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: white;
            padding: 12px 24px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 14px;
            z-index: 1000;
            animation: slideUp 0.3s ease;
        }

        /* Botón de ajustes */
        .settings-button {
            position: fixed;
            top: 40px;
            right: 40px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .settings-button:hover {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.9);
            transform: rotate(90deg);
            box-shadow: 0 4px 20px rgba(255, 255, 255, 0.1);
        }

        /* Modal de ajustes */
        .settings-modal, .bluetooth-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }

        .settings-modal.show, .bluetooth-modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Lista de dispositivos Bluetooth */
        .scanning-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            color: rgba(255, 255, 255, 0.6);
        }

        .spinner {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid rgba(0, 102, 255, 0.8);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .devices-list {
            max-height: 300px;
            overflow-y: auto;
            margin: 10px 0;
        }

        .device-item {
            padding: 15px;
            margin: 8px 0;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .device-item:hover {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(0, 102, 255, 0.5);
            transform: translateX(5px);
        }

        .device-item.esp {
            border: 1px solid rgba(68, 255, 68, 0.3);
        }

        .device-item.esp:hover {
            background: rgba(68, 255, 68, 0.1);
            border: 1px solid rgba(68, 255, 68, 0.5);
        }

        .device-name {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
        }

        .device-type {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-left: 10px;
            padding: 2px 8px;
            background: rgba(0, 102, 255, 0.2);
            border-radius: 12px;
        }

        .device-item.esp .device-type {
            background: rgba(68, 255, 68, 0.2);
            color: rgba(68, 255, 68, 0.9);
        }

        .no-devices {
            text-align: center;
            padding: 30px;
            color: rgba(255, 255, 255, 0.5);
        }

        .no-devices p {
            margin: 0 0 10px 0;
            font-size: 16px;
        }

        .scan-button {
            padding: 10px 24px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(0, 102, 255, 0.1);
            color: rgba(0, 102, 255, 0.9);
            border: 1px solid rgba(0, 102, 255, 0.3);
        }

        .scan-button:hover {
            background: rgba(0, 102, 255, 0.2);
            box-shadow: 0 4px 20px rgba(0, 102, 255, 0.2);
        }

        .modal-content {
            background: rgba(20, 20, 30, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 0;
            width: 90%;
            max-width: 400px;
            max-height: 80vh;
            overflow: hidden;
            animation: slideUpModal 0.3s ease;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            letter-spacing: 1px;
        }

        .close-button {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.5);
            font-size: 28px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .close-button:hover {
            color: rgba(255, 255, 255, 0.9);
            transform: scale(1.1);
        }

        .modal-body {
            padding: 25px;
        }

        .setting-group {
            margin-bottom: 25px;
        }

        .setting-group label {
            display: block;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .setting-group input[type="text"],
        .setting-group input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .setting-group input[type="text"]:focus,
        .setting-group input[type="password"]:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(0, 102, 255, 0.5);
            box-shadow: 0 0 20px rgba(0, 102, 255, 0.2);
        }

        .password-input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-input-group input {
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            padding: 5px;
            transition: all 0.3s ease;
        }

        .toggle-password:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .time-input-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        input[type="range"] {
            flex: 1;
            -webkit-appearance: none;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            outline: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: rgba(0, 102, 255, 0.8);
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        input[type="range"]::-webkit-slider-thumb:hover {
            background: rgba(0, 102, 255, 1);
            box-shadow: 0 0 15px rgba(0, 102, 255, 0.5);
        }

        input[type="number"] {
            width: 60px;
            padding: 8px 10px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            text-align: center;
        }

        input[type="number"]:focus {
            outline: none;
            border: 1px solid rgba(0, 102, 255, 0.5);
        }

        .time-unit {
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
        }

        .setting-group small {
            display: block;
            margin-top: 8px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 12px;
        }

        .modal-footer {
            padding: 20px 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .save-button, .cancel-button {
            padding: 10px 24px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .save-button {
            background: rgba(68, 255, 68, 0.1);
            color: rgba(68, 255, 68, 0.9);
            border-color: rgba(68, 255, 68, 0.3);
        }

        .save-button:hover {
            background: rgba(68, 255, 68, 0.2);
            box-shadow: 0 4px 20px rgba(68, 255, 68, 0.2);
        }

        .cancel-button {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.6);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .cancel-button:hover {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.9);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUpModal {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translate(-50%, 20px);
            }
            to {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 1;
                transform: translate(-50%, 0);
            }
            to {
                opacity: 0;
                transform: translate(-50%, 20px);
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .main-button {
                width: 180px;
                height: 180px;
            }
            
            .main-button-text {
                font-size: 42px;
            }
            
            .secondary-button {
                width: 90px;
                height: 90px;
            }
        }
    </style>
</head>
<body>
    <!-- Fondo espacial -->
    <div class="space-background"></div>
    <div class="stars" id="stars"></div>

    <!-- Estado de conexión -->
    <div class="connection-status">
        <div class="status-text" id="statusText">DESCONECTADO</div>
        <div class="device-name" id="deviceNameDisplay" style="display: none;"></div>
    </div>

    <!-- Contenedor principal -->
    <div class="main-container">
        <!-- Botón principal ON/OFF -->
        <div class="main-button-container">
            <div class="main-button" id="mainButton">
                <div class="main-button-text" id="mainButtonText">OFF</div>
            </div>
        </div>

        <!-- Botón secundario START -->
        <div class="secondary-button-container">
            <div class="secondary-button" id="secondaryButton">
                <div class="secondary-button-text" id="secondaryButtonText">START</div>
                <div class="timer-text" id="timerText" style="display: none;"></div>
            </div>
        </div>
    </div>

    <!-- Botón de conexión -->
    <div class="connect-button-container">
        <button class="connect-button" id="connectButton">CONECTAR</button>
    </div>

    <!-- Botón de ajustes -->
    <button class="settings-button" id="settingsButton" title="Ajustes">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M12 1v6m0 6v6m4.22-13.22l4.24 4.24M1.54 1.54l4.24 4.24M20.46 20.46l-4.24-4.24M1.54 20.46l4.24-4.24M23 12h-6m-6 0H1"></path>
        </svg>
    </button>

    <!-- Modal de ajustes -->
    <div class="settings-modal" id="settingsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Ajustes</h2>
                <button class="close-button" id="closeSettings">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="setting-group">
                    <label for="deviceNameInput">Nombre de la controladora</label>
                    <input type="text" id="deviceNameInput" placeholder="ESP32_WROOM_32U_001" maxlength="30">
                    <small>Este nombre se mostrará al conectar por Bluetooth</small>
                </div>
                
                <div class="setting-group">
                    <label for="relay2TimeInput">Tiempo del segundo relé (segundos)</label>
                    <div class="time-input-group">
                        <input type="range" id="relay2TimeSlider" min="1" max="10" value="3">
                        <input type="number" id="relay2TimeInput" min="1" max="10" value="3">
                        <span class="time-unit">seg</span>
                    </div>
                    <small>Tiempo que permanecerá activo el relé secundario</small>
                </div>
                
                <div class="setting-group">
                    <label for="wifiSSIDInput">Red WiFi (SSID)</label>
                    <input type="text" id="wifiSSIDInput" placeholder="TERRENO" maxlength="32">
                    <small>Nombre de la red WiFi a la que se conectará</small>
                </div>
                
                <div class="setting-group">
                    <label for="wifiPasswordInput">Contraseña WiFi</label>
                    <div class="password-input-group">
                        <input type="password" id="wifiPasswordInput" placeholder="••••••••" maxlength="64">
                        <button type="button" class="toggle-password" id="togglePassword">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    <small>Contraseña de la red WiFi (mínimo 8 caracteres)</small>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="save-button" id="saveSettings">GUARDAR</button>
                <button class="cancel-button" id="cancelSettings">CANCELAR</button>
            </div>
        </div>
    </div>

    <!-- Modal de dispositivos Bluetooth -->
    <div class="bluetooth-modal" id="bluetoothModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Dispositivos Bluetooth</h2>
                <button class="close-button" id="closeBluetooth">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="scanning-indicator" id="scanningIndicator">
                    <div class="spinner"></div>
                    <span>Buscando dispositivos...</span>
                </div>
                
                <div class="devices-list" id="devicesList">
                    <!-- Los dispositivos se agregarán dinámicamente aquí -->
                </div>
                
                <div class="no-devices" id="noDevices" style="display: none;">
                    <p>No se encontraron dispositivos</p>
                    <small>Asegúrate de que el Bluetooth esté activado en ambos dispositivos</small>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="scan-button" id="scanButton">BUSCAR DISPOSITIVOS</button>
                <button class="cancel-button" id="cancelBluetooth">CANCELAR</button>
            </div>
        </div>
    </div>

    <script>
        // Crear estrellas dinámicas
        function createStars() {
            const starsContainer = document.getElementById('stars');
            const numberOfStars = 150;
            
            for (let i = 0; i < numberOfStars; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.animationDelay = Math.random() * 3 + 's';
                star.style.animationDuration = (Math.random() * 3 + 2) + 's';
                
                const size = Math.random() * 2;
                star.style.width = size + 'px';
                star.style.height = size + 'px';
                
                starsContainer.appendChild(star);
            }
        }
        
        createStars();

        // Estado de la aplicación
        let state = {
            connected: false,
            relay1: false,
            relay2: false,
            relay2Active: false,
            timerInterval: null,
            countdown: 0
        };

        // Configuraciones
        let settings = {
            deviceName: 'ESP32_WROOM_32U_001',
            relay2Time: 3,
            wifiSSID: 'TERRENO',
            wifiPassword: '11111111'
        };

        // Estado de conexión Bluetooth
        let selectedDevice = null;
        let pairedDevices = JSON.parse(localStorage.getItem('pairedDevices') || '[]');

        // Cargar configuraciones guardadas
        if (localStorage.getItem('esp32Settings')) {
            settings = JSON.parse(localStorage.getItem('esp32Settings'));
        }

        // Elementos DOM
        const statusText = document.getElementById('statusText');
        const deviceNameDisplay = document.getElementById('deviceNameDisplay');
        const mainButton = document.getElementById('mainButton');
        const mainButtonText = document.getElementById('mainButtonText');
        const secondaryButton = document.getElementById('secondaryButton');
        const secondaryButtonText = document.getElementById('secondaryButtonText');
        const timerText = document.getElementById('timerText');
        const connectButton = document.getElementById('connectButton');
        
        // Elementos de ajustes
        const settingsButton = document.getElementById('settingsButton');
        const settingsModal = document.getElementById('settingsModal');
        const closeSettings = document.getElementById('closeSettings');
        const saveSettings = document.getElementById('saveSettings');
        const cancelSettings = document.getElementById('cancelSettings');
        const deviceNameInput = document.getElementById('deviceNameInput');
        const relay2TimeInput = document.getElementById('relay2TimeInput');
        const relay2TimeSlider = document.getElementById('relay2TimeSlider');
        const wifiSSIDInput = document.getElementById('wifiSSIDInput');
        const wifiPasswordInput = document.getElementById('wifiPasswordInput');
        const togglePassword = document.getElementById('togglePassword');
        
        // Elementos de Bluetooth
        const bluetoothModal = document.getElementById('bluetoothModal');
        const closeBluetooth = document.getElementById('closeBluetooth');
        const cancelBluetooth = document.getElementById('cancelBluetooth');
        const scanButton = document.getElementById('scanButton');
        const devicesList = document.getElementById('devicesList');
        const scanningIndicator = document.getElementById('scanningIndicator');
        const noDevices = document.getElementById('noDevices');

        // Event Listeners
        connectButton.addEventListener('click', handleConnect);
        mainButton.addEventListener('click', toggleRelay1);
        secondaryButton.addEventListener('click', startRelay2);
        
        // Event Listeners de ajustes
        settingsButton.addEventListener('click', openSettings);
        closeSettings.addEventListener('click', closeSettingsModal);
        cancelSettings.addEventListener('click', closeSettingsModal);
        saveSettings.addEventListener('click', saveSettingsData);
        
        // Event Listeners de Bluetooth
        closeBluetooth.addEventListener('click', closeBluetoothModal);
        cancelBluetooth.addEventListener('click', closeBluetoothModal);
        scanButton.addEventListener('click', scanForDevices);
        
        // Cerrar modales al hacer click fuera
        bluetoothModal.addEventListener('click', (e) => {
            if (e.target === bluetoothModal) {
                closeBluetoothModal();
            }
        });

        // Toggle para mostrar/ocultar contraseña
        togglePassword.addEventListener('click', () => {
            const type = wifiPasswordInput.type === 'password' ? 'text' : 'password';
            wifiPasswordInput.type = type;
            
            // Cambiar ícono
            if (type === 'text') {
                togglePassword.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>`;
            } else {
                togglePassword.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>`;
            }
        });
        
        // Sincronizar slider y input numérico
        relay2TimeSlider.addEventListener('input', (e) => {
            relay2TimeInput.value = e.target.value;
        });
        
        relay2TimeInput.addEventListener('input', (e) => {
            let value = parseInt(e.target.value);
            if (value < 1) value = 1;
            if (value > 10) value = 10;
            e.target.value = value;
            relay2TimeSlider.value = value;
        });

        // Cerrar modal al hacer click fuera
        settingsModal.addEventListener('click', (e) => {
            if (e.target === settingsModal) {
                closeSettingsModal();
            }
        });

        // Efecto hover para los botones
        mainButton.addEventListener('mouseenter', () => {
            if (state.connected) {
                document.body.style.background = state.relay1 ? 
                    'radial-gradient(ellipse at center, #0a2e0a 0%, #000000 100%)' :
                    'radial-gradient(ellipse at center, #2e0a0a 0%, #000000 100%)';
            }
        });

        mainButton.addEventListener('mouseleave', () => {
            document.body.style.background = '';
        });

        function openSettings() {
            // Cargar valores actuales
            deviceNameInput.value = settings.deviceName;
            relay2TimeInput.value = settings.relay2Time;
            relay2TimeSlider.value = settings.relay2Time;
            wifiSSIDInput.value = settings.wifiSSID;
            wifiPasswordInput.value = settings.wifiPassword;
            
            // Mostrar modal
            settingsModal.classList.add('show');
        }

        function closeSettingsModal() {
            settingsModal.classList.remove('show');
        }

        function saveSettingsData() {
            // Obtener nuevos valores
            const newDeviceName = deviceNameInput.value.trim() || 'ESP32_WROOM_32U_001';
            const newRelay2Time = parseInt(relay2TimeInput.value);
            const newWifiSSID = wifiSSIDInput.value.trim() || 'TERRENO';
            const newWifiPassword = wifiPasswordInput.value || '11111111';
            
            // Validar contraseña WiFi
            if (newWifiPassword.length < 8) {
                showToast('La contraseña WiFi debe tener mínimo 8 caracteres');
                return;
            }
            
            // Actualizar configuraciones
            settings.deviceName = newDeviceName;
            settings.relay2Time = newRelay2Time;
            settings.wifiSSID = newWifiSSID;
            settings.wifiPassword = newWifiPassword;
            
            // Guardar en localStorage
            localStorage.setItem('esp32Settings', JSON.stringify(settings));
            
            // Actualizar UI si está conectado
            if (state.connected) {
                deviceNameDisplay.textContent = settings.deviceName;
            }
            
            // Si estamos conectados, enviar configuración a ESP32
            if (state.connected) {
                console.log(`Simulando: SET_NAME:${settings.deviceName}`);
                console.log(`Simulando: SET_RELAY2_TIME:${settings.relay2Time}`);
                console.log(`Simulando: SET_WIFI:${settings.wifiSSID}:${settings.wifiPassword}`);
                showToast('Configuración actualizada');
            } else {
                showToast('Configuración guardada localmente');
            }
            
            closeSettingsModal();
        }

        function handleConnect() {
            if (!state.connected) {
                // Siempre mostrar lista de dispositivos disponibles
                openBluetoothModal();
            } else {
                disconnect();
            }
        }

        function openBluetoothModal() {
            bluetoothModal.classList.add('show');
            scanForDevices();
        }

        function closeBluetoothModal() {
            bluetoothModal.classList.remove('show');
        }

        function scanForDevices() {
            // Mostrar indicador de búsqueda
            scanningIndicator.style.display = 'flex';
            noDevices.style.display = 'none';
            devicesList.innerHTML = '';
            scanButton.disabled = true;
            
            // Simular búsqueda de dispositivos (en la app real usaría Bluetooth API)
            setTimeout(() => {
                const simulatedDevices = [
                    { name: 'ESP32-A4CF12', address: '3C:8A:1F:62:03:14', type: 'ESP32' },
                    { name: 'ESP32-B7D823', address: '3C:8A:1F:62:03:15', type: 'ESP32' },
                    { name: 'ESP32-C9E934', address: '3C:8A:1F:62:03:16', type: 'ESP32' },
                    { name: 'Smartphone', address: '11:22:33:44:55:66', type: 'Phone' },
                    { name: 'Headphones', address: '77:88:99:AA:BB:CC', type: 'Audio' }
                ];
                
                displayDevices(simulatedDevices);
                scanningIndicator.style.display = 'none';
                scanButton.disabled = false;
            }, 2000);
        }

        function displayDevices(devices) {
            devicesList.innerHTML = '';
            
            if (devices.length === 0) {
                noDevices.style.display = 'block';
                return;
            }
            
            noDevices.style.display = 'none';
            
            devices.forEach(device => {
                const deviceItem = document.createElement('div');
                deviceItem.className = 'device-item';
                if (device.type === 'ESP32') {
                    deviceItem.classList.add('esp');
                }
                
                // Verificar si está emparejado
                const isPaired = pairedDevices.find(d => d.address === device.address);
                
                deviceItem.innerHTML = `
                    <div>
                        <div class="device-name">${device.name}</div>
                        <small style="color: rgba(255,255,255,0.4); font-size: 11px;">
                            ${device.address} ${isPaired ? '(Emparejado)' : ''}
                        </small>
                    </div>
                    <span class="device-type">${device.type}</span>
                `;
                
                deviceItem.addEventListener('click', () => {
                    selectDevice(device);
                });
                
                devicesList.appendChild(deviceItem);
            });
        }

        function selectDevice(device) {
            if (device.type === 'ESP32') {
                selectedDevice = device;
                closeBluetoothModal();
                
                // Iniciar proceso de conexión
                statusText.textContent = 'CONECTANDO...';
                statusText.className = 'status-text connecting';
                connectButton.disabled = true;
                
                // Simular proceso de conexión
                setTimeout(() => {
                    // Verificar si ya está emparejado
                    const isPaired = pairedDevices.find(d => d.address === device.address);
                    
                    if (!isPaired) {
                        // Primera vez - solicitar PIN
                        const pin = prompt(`Emparejando con ${device.name}\n\nIngrese el PIN de seguridad:\n(PIN por defecto: 123456)`);
                        
                        if (pin === '123456') {
                            // Emparejamiento exitoso
                            pairedDevices.push(device);
                            localStorage.setItem('pairedDevices', JSON.stringify(pairedDevices));
                            completeConnection(device);
                        } else if (pin !== null) {
                            // PIN incorrecto
                            showToast('PIN incorrecto - Conexión cancelada');
                            resetConnectionUI();
                        } else {
                            // Cancelado
                            resetConnectionUI();
                        }
                    } else {
                        // Ya emparejado - conectar directamente
                        completeConnection(device);
                    }
                }, 1000);
                
            } else {
                showToast('Selecciona un dispositivo ESP32');
            }
        }

        function completeConnection(device) {
            state.connected = true;
            selectedDevice = device;
            
            // Actualizar UI
            statusText.textContent = 'CONECTADO';
            statusText.className = 'status-text connected';
            connectButton.textContent = 'DESCONECTAR';
            connectButton.disabled = false;
            
            // Mostrar nombre configurado (no el nombre Bluetooth del dispositivo)
            deviceNameDisplay.textContent = settings.deviceName;
            deviceNameDisplay.style.display = 'block';
            
            // Habilitar botones inmediatamente
            mainButton.style.pointerEvents = 'auto';
            mainButton.style.opacity = '1';
            
            // Actualizar estado de los botones
            updateUI();
            
            // Simular comandos iniciales
            console.log(`✅ Conectado a: ${device.name} (${device.address})`);
            console.log('Enviando configuración inicial...');
            console.log(`Simulando: SET_NAME:${settings.deviceName}`);
            console.log(`Simulando: SET_RELAY2_TIME:${settings.relay2Time}`);
            console.log(`Simulando: SET_WIFI:${settings.wifiSSID}:${settings.wifiPassword}`);
            console.log('Simulando: GET_STATUS');
            
            showToast('Conectado exitosamente');
        }

        function resetConnectionUI() {
            statusText.textContent = 'DESCONECTADO';
            statusText.className = 'status-text';
            connectButton.textContent = 'CONECTAR';
            connectButton.disabled = false;
            deviceNameDisplay.style.display = 'none';
            selectedDevice = null;
        }

        function toggleConnection() {
            handleConnect();
        }

        function connect() {
            // Esta función ya no se usa directamente
            handleConnect();
        }

        function disconnect() {
            state.connected = false;
            state.relay1 = false;
            state.relay2 = false;
            state.relay2Active = false;
            selectedDevice = null;
            
            if (state.timerInterval) {
                clearInterval(state.timerInterval);
                state.timerInterval = null;
            }
            
            statusText.textContent = 'DESCONECTADO';
            statusText.className = 'status-text';
            connectButton.textContent = 'CONECTAR';
            deviceNameDisplay.style.display = 'none';
            
            updateUI();
        }

        function toggleRelay1() {
            console.log('toggleRelay1 llamado - Estado conexión:', state.connected);
            
            if (!state.connected) {
                showToast('No conectado');
                return;
            }
            
            state.relay1 = !state.relay1;
            console.log('Relay 1 cambiado a:', state.relay1);
            
            // Si apagamos relé 1, también apagamos relé 2
            if (!state.relay1) {
                state.relay2 = false;
                state.relay2Active = false;
                if (state.timerInterval) {
                    clearInterval(state.timerInterval);
                    state.timerInterval = null;
                }
            }
            
            updateUI();
            console.log(`Simulando: RELAY1_${state.relay1 ? 'ON' : 'OFF'}`);
        }

        function startRelay2() {
            console.log('startRelay2 llamado - Relay1:', state.relay1, 'Relay2Active:', state.relay2Active);
            
            if (!state.connected || !state.relay1 || state.relay2Active) {
                if (!state.connected) {
                    showToast('No conectado');
                } else if (!state.relay1) {
                    showToast('Primero enciende el sistema');
                }
                return;
            }
            
            state.relay2 = true;
            state.relay2Active = true;
            state.countdown = settings.relay2Time; // Usar tiempo configurado
            
            updateUI();
            console.log('Simulando: RELAY2_START');
            
            // Iniciar cuenta regresiva con el tiempo correcto
            timerText.textContent = state.countdown + 's';
            timerText.style.display = 'block';
            
            state.timerInterval = setInterval(() => {
                state.countdown--;
                
                if (state.countdown > 0) {
                    timerText.textContent = state.countdown + 's';
                } else {
                    // Timer terminado
                    clearInterval(state.timerInterval);
                    state.timerInterval = null;
                    state.relay2 = false;
                    state.relay2Active = false;
                    timerText.style.display = 'none';
                    updateUI();
                    console.log('Timer completado - Relay 2 apagado');
                }
            }, 1000);
        }

        function updateUI() {
            // Actualizar botón principal
            if (state.relay1) {
                mainButton.classList.add('on');
                mainButtonText.textContent = 'ON';
                
                // Habilitar botón secundario
                secondaryButton.classList.add('enabled');
            } else {
                mainButton.classList.remove('on');
                mainButtonText.textContent = 'OFF';
                
                // Deshabilitar botón secundario
                secondaryButton.classList.remove('enabled');
                secondaryButton.classList.remove('active');
            }
            
            // Actualizar botón secundario
            if (state.relay2Active) {
                secondaryButton.classList.add('active');
                secondaryButtonText.textContent = 'ACTIVO';
                timerText.style.display = 'block';
                timerText.textContent = state.countdown + 's';
            } else {
                secondaryButton.classList.remove('active');
                secondaryButtonText.textContent = 'START';
                timerText.style.display = 'none';
            }
        }

        function showToast(message) {
            // Crear toast temporal
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideDown 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }

        // Log inicial
        console.log('ESP32 Web Controller SKYN3T - Simulador iniciado');
        console.log('Diseño glassmorphism con fondo espacial');
    </script>
</body>
</html>