# Sistema de Chamados

Este é um sistema completo para gestão de chamados, com integração LDAP/Active Directory, painel TV, dashboard moderno, autenticação, e recursos avançados para equipes de suporte.

## Funcionalidades

- Login moderno e responsivo
- Dashboard com métricas, gráficos, ranking, timeline, exportação e dark mode
- Painel TV para exibição de chamados abertos, com tela cheia, som e animações
- Importação de usuários via LDAP/AD, evitando duplicidade
- Rotas protegidas e públicas para API
- **♿ Acessibilidade completa** (WCAG 2.1 Nível AA)
- **📱 Design responsivo mobile-first** para todos os dispositivos
- **🎨 Interface moderna** com componentes reutilizáveis
- **⌨️ Navegação por teclado** completa
- **🌙 Modo escuro** automático
- Documentação interna em `DOCUMENTACAO.md`

## Instalação
1. Clone o repositório:
   ```sh
   git clone https://github.com/elneves81/sistema-de-chamados.git
   cd sistema-de-chamados
   ```
2. Instale as dependências PHP:
   ```sh
   composer install
   ```
3. Instale as dependências JS:
   ```sh
   npm install
   ```
4. Configure o `.env` com dados do banco e LDAP/AD.
5. Execute as migrations:
   ```sh
   php artisan migrate
   ```
6. Inicie o servidor:
   ```sh
   php artisan serve
   ```

## LDAP/AD
- Configure os dados de conexão no menu lateral (apenas admin).
- Importe usuários facilmente pelo painel.

## Painel TV
- Exibe apenas chamados abertos.
- Botão para tela cheia.
- Som e animação para chamados recém-chegados.

## Dashboard
- Cards, gráficos, exportação, dark mode e visual profissional.

## Contribuição
Pull requests são bem-vindos! Para grandes mudanças, abra uma issue primeiro.

## Licença
 ELN

---

Para mais detalhes, consulte os arquivos:
- `DOCUMENTACAO.md` - Documentação completa do sistema
- `DESIGN_RESPONSIVIDADE_ACESSIBILIDADE.md` - Guia completo de melhorias de UI/UX
- `GUIA_RAPIDO_MELHORIAS.md` - Guia rápido de uso das melhorias

## 🎨 Melhorias Recentes

### ♿ Acessibilidade (WCAG 2.1 AA)
- Navegação por teclado completa
- ARIA labels e landmarks
- Contraste de cores adequado
- Suporte a leitores de tela
- Skip links e foco visível

### 📱 Responsividade
- Design mobile-first
- Sidebar com toggle animado
- Tabelas responsivas (cards em mobile)
- Área de toque adequada (44px mínimo)
- Breakpoints modernos

### 🎨 Design Moderno
- Design system com variáveis CSS
- Componentes reutilizáveis (botões, inputs, alertas, modais)
- Paleta de cores consistente
- Animações sutis e profissionais
- Modo escuro automático

**Veja o guia completo em `GUIA_RAPIDO_MELHORIAS.md`**

