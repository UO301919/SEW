
import xml.etree.ElementTree as ET
import argparse
from pathlib import Path

NAMESPACES = {"c": "http://www.uniovi.es"}

# -------------------------------
# Lectura de datos desde XML
# -------------------------------
def obtener_valor(elemento, ruta, tipo=float):
    nodo = elemento.find(ruta, NAMESPACES)
    if nodo is None or nodo.text is None:
        return None
    return tipo(nodo.text.strip()) if tipo else nodo.text.strip()

def cargar_circuito(xml_file):
    raiz = ET.parse(xml_file).getroot()
    nombre = obtener_valor(raiz, ".//c:nombre", tipo=None) or "Circuito"

    origen = raiz.find(".//c:puntoOrigen", NAMESPACES)
    if origen is None:
        raise ValueError("No existe <puntoOrigen> en el XML")

    inicio = (
        obtener_valor(origen, "c:longitudOrigen"),
        obtener_valor(origen, "c:latitudOrigen"),
        obtener_valor(origen, "c:altitudOrigen"),
    )

    puntos = [inicio]

    for tramo in raiz.findall(".//c:tramos/c:tramo", NAMESPACES):
        lon = obtener_valor(tramo, "c:longitud")
        lat = obtener_valor(tramo, "c:latitud")
        alt = obtener_valor(tramo, "c:altitud")
        puntos.append((lon, lat, alt))

    return nombre, puntos

# -------------------------------
# Generación del contenido KML
# -------------------------------
def formatear_coordenadas(puntos, cerrar=False):
    if cerrar and puntos[0] != puntos[-1]:
        puntos = puntos + [puntos[0]]
    return " ".join(f"{lon:.6f},{lat:.6f},{alt:.2f}" for lon, lat, alt in puntos)

def crear_kml(nombre, puntos, cerrar=False):
    coords = formatear_coordenadas(puntos, cerrar)
    return f"""<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
 <Document>
   <name>{nombre}</name>

   <Style id="circuitoStyle">
     <LineStyle>
       <color>ff0000ff</color>
       <width>4</width>
     </LineStyle>
   </Style>

   <Placemark>
     <name>{nombre} - Trazado</name>
     <styleUrl>#circuitoStyle</styleUrl>
     <LineString>
       <tessellate>1</tessellate>
       <altitudeMode>absolute</altitudeMode>
       <coordinates>
         {coords}
       </coordinates>
     </LineString>
   </Placemark>

   <Placemark>
     <name>Salida / Meta</name>
     <Point>
       <coordinates>{puntos[0][0]:.6f},{puntos[0][1]:.6f},{puntos[0][2]:.2f}</coordinates>
     </Point>
   </Placemark>

 </Document>
</kml>
"""

# -------------------------------
# Programa principal
# -------------------------------
def main():
    parser = argparse.ArgumentParser(description="Genera un archivo KML desde circuitoEsquema.xml")
    parser.add_argument("-i", "--input", default="circuitoEsquema.xml", help="XML de entrada")
    parser.add_argument("-o", "--output", default="circuito.kml", help="KML de salida")
    parser.add_argument("--cerrar", action="store_true", help="Cerrar el trazado volviendo al inicio")
    args = parser.parse_args()

    if not Path(args.input).exists():
        raise SystemExit(f"No se encuentra el archivo {args.input}")

    nombre, puntos = cargar_circuito(args.input)
    kml_texto = crear_kml(nombre, puntos, cerrar=args.cerrar)

    Path(args.output).write_text(kml_texto, encoding="utf-8")
    print(f"Archivo KML creado: {args.output} con {len(puntos)} puntos")

if __name__ == "__main__":
    main()
