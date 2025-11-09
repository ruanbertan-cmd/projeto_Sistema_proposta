# 🧮 Sistema de Validação de Lote Mínimo

[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
![Status](https://img.shields.io/badge/Status-Em%20Desenvolvimento-yellow)
![License](https://img.shields.io/badge/Licen%C3%A7a-Uso%20Interno-red)
![Author](https://img.shields.io/badge/Autor-Ruan%20Bertan-blue)

---

## 🧠 Sobre o Projeto

Sistema desenvolvido para **automatizar e organizar a validação de propostas comerciais**, garantindo que os volumes informados atendam aos **lotes mínimos definidos** por:

> **Formato • Volume • Tipologia • Unidade de Medida • Polo • Acabamento**

O objetivo é evitar inconsistências durante o cadastro de propostas e assegurar conformidade com os critérios de lote mínimo estabelecidos pela empresa.

---

## ⚙️ Tecnologias Utilizadas

- 🐘 **PHP 8+**
- 🧮 **MySQL**
- 🎨 **HTML5 / CSS / JavaScript**

---

## 🎯 Objetivo do Sistema

O sistema realiza cruzamentos automáticos com a planilha de **Lotes Mínimos**, avaliando se os dados informados na proposta cumprem as regras estabelecidas.  
A resposta é apresentada ao usuário em tempo real com base nos resultados de verificação.

---

## 📊 Exemplos de Situações

### ✅ Situação 1 — Exceção de Formato  
Mesmo que o formato não esteja na planilha, é considerado válido (formato de corte).

| Campo       | Status                   |
|--------------|--------------------------|
| Formato      | ❌ Não está na planilha  |
| Volume       | ✅ Ok                    |
| Tipologia    | ✅ Ok                    |
| Unidade      | ✅ Ok                    |
| Polo         | ✅ Ok                    |
| Acabamento   | ✅ Ok                    |

**Resultado:** `Ok, proposta cadastrada considerando como formato de corte.`

---

### ✅ Situação 2 — Tudo Ok
Todos os campos estão na planilha e o volume atende o mínimo.

**Resultado:** `Ok, volume atende o lote mínimo definido.`

---

### 🚫 Situação 3 — Volume Abaixo do Lote Mínimo  
O volume informado é inferior ao exigido.

**Resultado:** `Bloqueado, volume inferior ao lote mínimo de 4000 m².`

---

### 🚫 Situação 4 — Tipologia Inválida  
Tipologia não presente na planilha. Volume desconsiderado.

**Resultado:** `Bloqueado, tipologia não está presente para o formato/unidade/polo informado.`

---

🧩 O sistema cobre **diversas combinações possíveis** (Unidade de Medida, polo, acabamento, tipologia, Formato e Volume), bloqueando automaticamente casos inconsistentes.
(Ao todo foi mapeado 16 situações diferentes envolvendo esses 6 campos. Onde o sistema esta considerando para aceitar solicitações validadas).

---

## 📂 Estrutura Simplificada do Projeto

```bash
/public
  ├── uploads/
  ├── proposta_cadastro.php
  ├── proposta_detalhes.php
  ├── proposta_consulta.php
  ├── proposta_aprovacao.php 
  ├── proposta_lote.php 
  ├── upload_lote.php 
  └── login.php

/src
  ├── config/
  │    └── conexao.php
  ├── controllers/
  │    ├── aprovar_proposta.php
  │    ├── rejeitar_proposta.php
  │    └── comentario_proposta.php
  └── functions/
       └── verificar_lote_db.php
```

🧱 Próximos Passos

🔹 Implementar popup de validação rápida (consulta simplificada)

🔹 Melhorar feedback visual das validações

🔹 Criar logs de verificação para auditoria

🔹 Criar endpoint de API para integração futura

---

👨‍💻 Autor

Ruan Bertan
Desenvolvedor e idealizador do sistema de Validação de Lote Mínimo
📍 Projeto interno da Eliane Revestimentos (uso corporativo)

---