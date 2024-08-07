# Desafio Fullstack

## Tecnologias
* Backend
  * Symfony
  * MySQL
  * OpenAPI (Swagger)
* Frontend
  * Next.js
  * Ant Design
  * TailwindCSS
  * React Query

## Documentação

Está na pasta api.

## Como Rodar
#### Passo a Passo

* Copiar os arquivos env.example dentro das pastas de Backend e Frontend e mudar o que for necessário
* Rodar o docker-compose up -d

- Acessar http://localhost:8001 para o Frontend
- Portas de Database expostas pela porta 13306
- Health check é necessário para saber o estado do serviço (Backend e Database devem estar healthy para rodar o Frontend de forma apropriada)


## Considerações

Projeto de baixa dificuldade porem com necessidade de atenção aos detalhes de segurança para manter um padrão minimo dentro do PHP, expondo o minimo do sistema e respeitando regras de processamento de dados vindos de fontes não confiaveis (Externas: Usuario)

Para o frontend segue-se o mesmo padrão para que possamos evitar um ataque de SSRF e XSS Injection.

## Decisões

Backend em usar algumas ferramentas já bem maduras.

Frontend segue do mesmo principio porem usar o Grid pra ter uma menor complexidade pra lidar com layout mobile