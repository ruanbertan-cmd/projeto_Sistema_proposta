import os
import pandas as pd

# === 0. Caminhos
base_dir = os.path.dirname(os.path.abspath(__file__))
pasta_data = os.path.join(base_dir, '../data')
os.makedirs(pasta_data, exist_ok=True)  # garante que a pasta exista

caminho_lote = os.path.join(
    pasta_data, 'Lotes_mínimos_por Bitola_26_08_25(Substituir_arquivo_manualmente_para_atualizar).xlsx'
)
caminho_itens = r'X:\transf\LISTATOT_ep010r_ITENS.csv'
caminho_saida = os.path.join(pasta_data, 'lote_minimo.xlsx')

# === 1. Lê a planilha principal (lote mínimo)
df_lotes = pd.read_excel(
    caminho_lote,
    header=1,
    names=['emp', 'uni', 'uni_fabril', 'bitola', 'formato', 'descricao',
           'sit', 'lote', 'lote_alternativo1', 'lote_alternativo2']
)

# === 2. Define o polo
def definir_polo(row):
    if row['emp'] == 1 and row['uni'] in [1, 31, 41, 63]:
        return 'SC'
    elif row['emp'] == 44 and row['uni'] == 1:
        return 'SC'
    elif row['emp'] == 13 and row['uni'] == 1:
        return 'BA'
    elif row['emp'] == 42 and row['uni'] == 1:
        return 'PB'
    elif row['emp'] == 45 and row['uni'] == 1:
        return 'RN'
    else:
        return 'Outro'

# Adiciona a coluna 'polo' logo após 'uni'
df_lotes.insert(df_lotes.columns.get_loc('uni') + 1, 'polo', df_lotes.apply(definir_polo, axis=1))

# === 3. Lê a tabela auxiliar (LISTATOT)
df_itens = pd.read_csv(
    caminho_itens,
    sep=';',
    encoding='latin1',
    low_memory=False
)

# === 4. Reduz e prepara para merge
df_itens_reduzido = df_itens[['Cod Bit', 'Tecnologia', 'Un']].copy()
df_itens_reduzido = df_itens_reduzido.drop_duplicates(subset='Cod Bit')

# Limpa colunas de chave
df_lotes['bitola'] = df_lotes['bitola'].astype(str).str.strip()
df_itens_reduzido['Cod Bit'] = df_itens_reduzido['Cod Bit'].astype(str).str.strip()

# === 5. Merge tipo PROCX
df_completo = pd.merge(df_lotes, df_itens_reduzido, left_on='bitola', right_on='Cod Bit', how='left').copy()
df_completo.drop(columns=['Cod Bit'], inplace=True)

# === 6. Ajustes finais
if 'Un' in df_completo.columns:
    df_completo['Un'] = df_completo['Un'].astype(str).str.upper()

# Reorganiza colunas (Tecnologia e Un após formato)
colunas = list(df_completo.columns)
pos_formato = colunas.index('formato')
for col in ['Tecnologia', 'Un']:
    if col in colunas:
        colunas.remove(col)
        colunas.insert(pos_formato + 1, col)
        pos_formato += 1

df_completo = df_completo[colunas]

# === 7. Salva resultado final
df_completo.to_excel(caminho_saida, index=False)

print(f'✅ Planilha combinada com sucesso!\n💾 Arquivo salvo em: {caminho_saida}')
print(df_completo.head(10))