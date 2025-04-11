# symfony composer req workflow

# En el archivo de configuracion config/packages/workflow.yaml podemos definir los flujos que queramos para un estado. Despues podemos generar imagenes con el flujo con el comando:
# symfony console workflow:dump comment | dot -Tpng -o workflow.png

# Es necesario instalar https://www.graphviz.org/ y crear la variable de sistema