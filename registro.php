<?php
/**
 * NOTA QA: este archivo era una copia casi idéntica de login.php (no registra usuarios,
 * solo inicia sesión). Para no mantener dos implementaciones de autenticación (y que una
 * quede sin parchar), ahora delega en login.php. Si se necesita un registro real,
 * debe implementarse aquí con validación, hash de contraseña y CSRF.
 */
require_once __DIR__ . '/login.php';
