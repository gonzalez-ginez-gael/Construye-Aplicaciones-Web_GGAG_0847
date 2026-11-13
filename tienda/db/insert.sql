USE tienda_db;

INSERT INTO categorias (nombre) SELECT 'Supersport' WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Supersport');
INSERT INTO categorias (nombre) SELECT 'Naked'      WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Naked');
INSERT INTO categorias (nombre) SELECT 'Touring'    WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Touring');
INSERT INTO categorias (nombre) SELECT 'Adventure'  WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = 'Adventure');

-- Supersport
INSERT INTO marcas (nombre, categoria_id)
SELECT t.n, (SELECT id FROM categorias WHERE nombre = 'Supersport' LIMIT 1) FROM (
  SELECT 'Yamaha YZF-R1' AS n UNION ALL SELECT 'Yamaha YZF-R6' UNION ALL SELECT 'Ducati Panigale V4'
  UNION ALL SELECT 'Ducati Panigale V2' UNION ALL SELECT 'Kawasaki Ninja ZX-10R' UNION ALL SELECT 'Kawasaki Ninja ZX-6R'
  UNION ALL SELECT 'Honda CBR1000RR-R' UNION ALL SELECT 'Suzuki GSX-R750' UNION ALL SELECT 'BMW S 1000 RR'
  UNION ALL SELECT 'Aprilia RSV4'
) t WHERE NOT EXISTS (SELECT 1 FROM marcas WHERE nombre = t.n);

-- Naked
INSERT INTO marcas (nombre, categoria_id)
SELECT t.n, (SELECT id FROM categorias WHERE nombre = 'Naked' LIMIT 1) FROM (
  SELECT 'Yamaha MT-09' AS n UNION ALL SELECT 'Yamaha MT-07' UNION ALL SELECT 'Ducati Monster'
  UNION ALL SELECT 'Ducati Streetfighter V4' UNION ALL SELECT 'Kawasaki Z900' UNION ALL SELECT 'Kawasaki Z650'
  UNION ALL SELECT 'Honda CB1000R' UNION ALL SELECT 'Triumph Street Triple' UNION ALL SELECT 'BMW S 1000 R'
  UNION ALL SELECT 'KTM 890 Duke'
) t WHERE NOT EXISTS (SELECT 1 FROM marcas WHERE nombre = t.n);

-- Touring
INSERT INTO marcas (nombre, categoria_id)
SELECT t.n, (SELECT id FROM categorias WHERE nombre = 'Touring' LIMIT 1) FROM (
  SELECT 'Yamaha Tracer 9 GT' AS n UNION ALL SELECT 'Ducati Multistrada V4' UNION ALL SELECT 'Kawasaki Ninja 1000SX'
  UNION ALL SELECT 'Honda VFR800F' UNION ALL SELECT 'BMW R 1250 RT' UNION ALL SELECT 'BMW S 1000 XR'
  UNION ALL SELECT 'Suzuki GSX-S1000GT' UNION ALL SELECT 'Triumph Tiger Sport 660' UNION ALL SELECT 'KTM 890 SMT'
  UNION ALL SELECT 'Honda NT1100'
) t WHERE NOT EXISTS (SELECT 1 FROM marcas WHERE nombre = t.n);

-- Adventure
INSERT INTO marcas (nombre, categoria_id)
SELECT t.n, (SELECT id FROM categorias WHERE nombre = 'Adventure' LIMIT 1) FROM (
  SELECT 'Yamaha Ténéré 700' AS n UNION ALL SELECT 'Ducati DesertX' UNION ALL SELECT 'Kawasaki Versys 650'
  UNION ALL SELECT 'Honda Africa Twin' UNION ALL SELECT 'BMW R 1250 GS' UNION ALL SELECT 'BMW F 850 GS'
  UNION ALL SELECT 'Suzuki V-Strom 1050' UNION ALL SELECT 'Triumph Tiger 900' UNION ALL SELECT 'KTM 890 Adventure'
  UNION ALL SELECT 'Aprilia Tuareg 660'
) t WHERE NOT EXISTS (SELECT 1 FROM marcas WHERE nombre = t.n);

-- Verificación: cada categoría debe mostrar al menos 10
SELECT c.nombre, COUNT(m.id) AS modelos
FROM categorias c LEFT JOIN marcas m ON m.categoria_id = c.id
GROUP BY c.id, c.nombre;