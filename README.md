# Avaliação Desenvolvedor - Assim Saúde v2.0

Projeto exemplo em PHP + MySQL (MVC simples) com front-end em HTML/CSS/JS.
Idioma: Português (pt-BR)

---

## Estrutura do repositório

- `frontend/` - arquivos HTML, CSS e JS (aplicação cliente).
- `backend/` - backend em PHP (API REST simples usando PDO).
- `db/` - script SQL para criar o banco e tabelas.
- `README.md` - este arquivo.

---

## Ferramentas utilizadas

- PHP 7.4+ (ou PHP 8)
- MySQL / MariaDB
- XAMPP / WAMP / EasyPHP (sugestão)
- Git
- Navegador moderno

---

## Como rodar localmente (passo a passo)

1. Instale XAMPP/WAMP/EasyPHP com Apache + PHP + MySQL.
2. Copie a pasta `frontend/` e `backend/` para o diretório público do servidor (ex: `C:\xampp\htdocs\assim-saude-v2\`).
   Alternativamente use o servidor PHP embutido para testar o backend.

3. Crie o banco de dados:
   - Abra o `phpMyAdmin` ou MySQL CLI e execute o script `db/init.sql`.
   - Nome do banco criado no script: `assim_saude`.

4. Configure conexão com o banco:
   - Edite `backend/config.php` e ajuste `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` conforme seu ambiente.

5. Acesse:
   - Frontend: `http://localhost/assim-saude-v2/frontend/index.html`
   - Backend (API): `http://localhost/assim-saude-v2/backend/`

---

## Endpoints importantes (backend)

- `backend/api/cargos.php` - CRUD de cargos (ação via query param `action`: list, create, update, delete, search)
- `backend/api/funcionarios.php` - CRUD de funcionários (valida CPF, busca, list)
- `backend/api/relatorio.php` - lista relatório de funcionários com filtros

As chamadas são feitas via `fetch` do frontend.

---

## Regras de negócio

- CPF deve ser válido (validação no backend).
- CPF deve ser único entre funcionários.

---

## Observações

- Projeto simples para avaliação. Não usar em produção sem ajustes de segurança (autenticação, validações adicionais, prepared statements — já usamos PDO preparado — e CORS conforme ambiente).
- Para subir no GitHub: crie um repositório, adicione remote e faça push:
  ```
  git init
  git add .
  git commit -m "Avaliação Assim Saúde v2.0 - versão inicial"
  git remote add origin https://github.com/SEU_USUARIO/REPO.git
  git branch -M main
  git push -u origin main
  ```

