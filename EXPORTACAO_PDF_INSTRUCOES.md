# Instruções - Exportação PDF do Dashboard

## Como funciona agora

Quando você clica no botão **PDF** (vermelho) na seção "EXPORTAÇÃO":

1. **Modal aparece sobre a página** (fundo escuro semitransparente)
2. **Carrega a pré-visualização** do relatório com:
   - Filtros aplicados mostrados no topo
   - KPIs (Total, Abertos, Em andamento, Resolvidos hoje, Tempo médio)
   - Tabela "Chamados por Categoria"
   - Tabela "Chamados por Prioridade"
3. **Barra de ações no topo do modal** com 3 botões:
   - 🖨️ **Imprimir** → Abre diálogo de impressão do navegador (pode salvar como PDF)
   - 📥 **Baixar PDF** → Faz download do PDF via backend (DomPDF)
   - ❌ **Fechar** → Fecha o modal e volta ao dashboard

## Solução de problemas

### Se o modal não aparecer:

1. **Limpe o cache do navegador**: Ctrl+Shift+R (Linux/Windows) ou Cmd+Shift+R (Mac)
2. **Verifique o console do navegador** (F12 → Console) para ver erros
3. **Confirme que os assets foram compilados**: 
   ```bash
   npm run build
   ```

### Se aparecer erro "Modal não encontrado":

- O modal HTML está na view `dashboard.blade.php`
- Certifique-se de que você está na página `/dashboard` e não em outra rota

### Debug adicional:

Abra o console do navegador (F12) antes de clicar em PDF. Você verá:
- "Abrindo modal preview. URL: ..." → confirma que está tentando abrir
- Se aparecer "Modal não encontrado no DOM" → problema no HTML da página

## Arquivos modificados

- `resources/views/dashboard.blade.php` → Modal HTML adicionado
- `resources/js/dashboard-modern.js` → Lógica de abertura do modal
- Assets compilados em: `public/build/assets/dashboard-modern-63125788.js`

## Testar agora

1. Acesse: http://suportesaude.guarapuava.pr.gov.br:8083/dashboard
2. Force reload: **Ctrl+Shift+R**
3. Clique no botão vermelho **PDF** na seção "EXPORTAÇÃO"
4. O modal deve aparecer imediatamente com a pré-visualização

---

**Nota**: Se ainda não funcionar após limpar o cache, pode ser que o servidor esteja servindo a versão antiga do JS em cache. Nesse caso, reinicie o servidor Laravel ou limpe o cache do Laravel com `php artisan cache:clear`.
