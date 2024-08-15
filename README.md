## Testes
Realizei alguns testes unitários para garantir que as funções estavam funcionando conforme o esperado. Atualizei os testes necessários e adicionei novos testes de integração para as novas regras adicionadas nos fluxos já existentes.

## Comentário e Observações
- Nos envios de notificações optei por não atrapalhar o fluxo de finalização da resposta, pois, acredito que deve ser algo que deva ser tratado em background dentro do próprio sistema, ou, se não for algo a ser tratado via código (como por exemplo um 404, ou um número que não foi cadastrado) deve ser enviado alguma notificação para o destinário informando a falha no envio e o motivo.

- Optei por não fazer a primeira solucação que veio na cabeça que seria criar os atributos tinyInteger para cada situação (ex.: enviar_email_finalizacao_dono_formulario) e criar a lógica individual para cada um dos casos pois dessa forma a implementação com certeza seria mais rápida e menos trabalhosa, porém, não teria a flexibilidade que acredito que a solução que implementei possui

- Optei também por não fazer uma tabela de com as configurações e nem uma tabela para registrar, por exemplo, os envios de mensagens no whatsapp, pois (através dos fields) que o time prefere evitar novas tabelas e relacionamentos (posso estar errada rs). Adicionei as configurações de notificação como atributo na tabela de forms, dessa forma fica mais performático. Por não ser uma lógica complexa, acredito que seja viável.

- Optei por trabalhar com Enums e Classes para garantir a integridade do código. Quando trabalhamos com atributo json, não existem mts regras, pode ser permitido adicionar qualquer tipo de valor, desde que seja um json. Isso acaba oferecendo um risco grande, pois, é esperada uma determinada estrutura daquele valor para que o código funcione quando existem regras de negócios que dependam disso. Dessa forma os enums e classes acabam documentando e restringindo um pouco essa liberdade, diminuindo o risco de erros.

- Fiz as trativas dos erros que consegui mapear nesse período de tempo.

- Adicionei alguns comentários explicando algumas lógicas e também comentários avulsos de como eu costumo codar.

- Os testes de queue e email tem uma versão com fake queue e uma versão utilizando a queue da aplicação.

- Na construção do código acabei refazendo algumas vezes. Geralmente primeiro eu faço funcionar e depois eu refatoro. Porém, não gostei da primeira e nem da segunda refatoração, finalizando na terceira vez.

- Para realizar o teste de envio de e-mail utilizei o mailtrap.

- Alterações no .env ou .env.testing executar os comandos `php artisan config:clear && php artisan config:cache` (apanhei por falta disso)

- Acredito que houveram muitos novos arquivos, porém, a maioria é "formalidade".

- Sobre o chatgpt gosto de utilizar para me ajudar a escrever testes, pois, temos que mockar muitas informações. Utilizo para tirar dúvidas no geral. Costumo pedir exemplo e analisar para ter um direcionamento quando estou meio perdida. E por fim, utilizo qnd temrino de escrever um código e quero saber se posso refatorar para ficar mais simples (não faço isso toda vez por ou por não lembrar ou por estar satisfeita com o código que escrevi).
