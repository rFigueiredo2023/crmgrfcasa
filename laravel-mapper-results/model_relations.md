# Relações entre Models

| Model | Relações |
|-------|----------|
| Arquivo |  belongsTo: 2 |
| Atendimento |  belongsTo: 2 |
| Cliente |  hasMany: 4, belongsTo: 2, hasOne: 1 |
| Historico |  belongsTo: 1 |
| InscricaoEstadual |  belongsTo: 1 |
| Lead |  belongsTo: 1 |
| Mensagem |  belongsTo: 2 |
| Segmento |  hasMany: 1 |
| Transportadora |  belongsTo: 1 |
| User | *Nenhuma relação encontrada* |
| Veiculo |  belongsTo: 1 |
