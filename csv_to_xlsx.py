import pandas as pd

# Reading the csv file
df = pd.read_csv('data.csv', names = ['Nama', 'Harga', 'Link'])

# Sort
df = df.sort_values(['Harga'], ascending=[False])

# Saving xlsx file
writer = pd.ExcelWriter('data_final.xlsx')
df.to_excel(writer, index = False, startrow=2)

writer.save()
