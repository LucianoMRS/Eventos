# Sistema Web de Gerenciamento de Eventos Acadêmicos

Feito com PHP (orientado a objetos + PDO), MySQL.

## Como executar (XAMPP)

1. Copie a pasta `eventos` para `C:\xampp\htdocs\`.
2. Inicie o **Apache** e o **MySQL** no XAMPP.
3. Abra o phpMyAdmin (`http://localhost/phpmyadmin`), vá em **Importar** e escolha o arquivo `banco.sql`.
4. Acesse `http://localhost/eventos/`.

> Se o seu MySQL tiver senha, altere os dados em `includes/conexao.php`.

## Estrutura

```
eventos/
├── banco.sql                 script do banco de dados
├── estilo.css                visual do sistema
├── index.php                 página inicial
├── eventos.php               listar e pesquisar eventos
├── evento_form.php           cadastrar e editar evento
├── evento_excluir.php        excluir evento
├── turnos.php                cadastrar e listar os turnos de um evento
├── turno_excluir.php         excluir turno
├── participantes.php         listar e pesquisar participantes
├── participante_form.php     cadastrar e editar participante
├── participante_excluir.php  excluir participante
├── inscricoes.php            registrar, listar e pesquisar inscrições
├── inscricao_cancelar.php    cancelar inscrição
├── relatorios.php            relatórios
├── classes/                  Evento, Turno, Participante e Inscricao (POO)
└── includes/                 conexao, iniciar, cabecalho e rodape
```

## Etapa 1 – Requisitos

**Funcionais**
- RF01 – Cadastrar, editar, excluir e listar eventos. Depois de cadastrar o evento, o coordenador cadastra os turnos dele (Manhã, Tarde e/ou Noite), cada um com o seu horário e as suas vagas.
- RF02 – Cadastrar, editar, excluir e consultar participantes.
- RF03 – Registrar, consultar e cancelar inscrições. A inscrição é feita em um turno do evento.
- RF04 – Pesquisar eventos, participantes e inscrições.
- RF05 – Gerar relatórios (totais e ocupação por evento).

**Não funcionais**
- RNF01 – Usar PHP e MySQL.
- RNF02 – Interface web simples, que funciona no navegador.
- RNF03 – Garantir a integridade dos dados (chaves estrangeiras, e-mail único, sem inscrição duplicada, sem choque de horário para o participante, controle de vagas por turno).
- RNF04 – Código organizado com `require` e classes.

## Etapa 2 – Modelagem

**Casos de uso** — Ator: *Coordenador acadêmico*. Ele realiza: Gerenciar eventos, Gerenciar participantes, Registrar inscrição, Consultar inscrições, Cancelar inscrição e Gerar relatórios.

**MER**

```
EVENTOS (1) ----< TURNOS (1) ----< INSCRICOES >---- (1) PARTICIPANTES
```
Um evento tem vários turnos; cada turno tem várias inscrições; um participante pode ter várias inscrições (relação N:N entre turnos e participantes, resolvida pela tabela `inscricoes`).

**Dicionário de dados**

| Tabela | Campo | Tipo | Observação |
|---|---|---|---|
| eventos | id | INT | chave primária, auto incremento |
| eventos | titulo | VARCHAR(150) | obrigatório |
| eventos | descricao | TEXT | opcional |
| eventos | data_evento | DATE | obrigatório |
| eventos | local_evento | VARCHAR(100) | obrigatório |
| turnos | id | INT | chave primária, auto incremento |
| turnos | evento_id | INT | chave estrangeira → eventos.id |
| turnos | turno | VARCHAR(10) | Manhã, Tarde ou Noite |
| turnos | hora_inicio | TIME | obrigatório |
| turnos | hora_fim | TIME | obrigatório, depois do início |
| turnos | vagas | INT | obrigatório, maior que 0 (vagas de cada turno) |
| participantes | id | INT | chave primária, auto incremento |
| participantes | nome | VARCHAR(100) | obrigatório |
| participantes | email | VARCHAR(100) | obrigatório, único |
| participantes | telefone | VARCHAR(20) | opcional |
| participantes | curso | VARCHAR(100) | opcional |
| inscricoes | id | INT | chave primária, auto incremento |
| inscricoes | turno_id | INT | chave estrangeira → turnos.id |
| inscricoes | participante_id | INT | chave estrangeira → participantes.id |
| inscricoes | data_inscricao | DATETIME | preenchido automaticamente |

Regras no banco: o par (`evento_id`, `turno`) é único em `turnos` (o evento não repete o mesmo turno) e o par (`turno_id`, `participante_id`) é único em `inscricoes` (o participante não se inscreve duas vezes no mesmo turno).

Regra no PHP (`Inscricao::registrar`): o participante não pode ter dois turnos **no mesmo dia com horários que se sobrepõem**, mesmo que sejam de eventos diferentes. Horários que apenas se encostam (um termina às 12:00 e o outro começa às 12:00) não são conflito.

Para essa regra nunca ser quebrada depois da inscrição: um evento que já tem inscritos não pode mudar de data, e um turno que já tem inscritos não pode ser excluído (cancele as inscrições antes).

## Etapa 4 – Roteiro de testes

- [ ] **Cadastro:** cadastrar evento e participante; tentar salvar com campos vazios e com e-mail repetido (deve mostrar erro).
- [ ] **Turnos:** cadastrar um evento e, na tela de turnos, cadastrar Manhã, Tarde e Noite com horários e vagas diferentes; tentar cadastrar sem escolher o turno, com término antes do início, com 0 vagas e o mesmo turno duas vezes no mesmo evento (deve mostrar erro).
- [ ] **Consulta:** usar a busca em eventos, participantes e inscrições.
- [ ] **Atualização:** editar um evento e um participante; tentar mudar a data de um evento que já tem inscritos (deve mostrar erro).
- [ ] **Exclusão:** excluir evento, participante, turno sem inscritos e cancelar inscrição. Tentar excluir um turno com inscritos (deve mostrar erro).
- [ ] **Inscrição:** inscrever o mesmo participante duas vezes no mesmo turno do evento (erro) e inscrever em turno lotado (erro). Inscrever o mesmo participante em dois turnos diferentes do mesmo evento, sem sobreposição de horário (deve funcionar).
- [ ] **Choque de horário:** cadastrar dois eventos na mesma data com turnos de horários que se sobrepõem (ex.: 08:00–12:00 e 10:00–13:00), inscrever o participante em um e tentar no outro (deve mostrar erro).
- [ ] **Relatório:** conferir se os totais e a ocupação de cada turno batem com os dados.
