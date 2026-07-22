--
-- PostgreSQL database dump
--

\restrict up4nyjkYZnRwFf56TUu2FaLIQrCxracjIfOrhuCgEiBY5lYvIhsVKw0KxPNHCa4

-- Dumped from database version 15.18 (Debian 15.18-1.pgdg13+1)
-- Dumped by pg_dump version 15.18 (Debian 15.18-1.pgdg13+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: registrar_fecha_resolucion(); Type: FUNCTION; Schema: public; Owner: incidencias_user
--

CREATE FUNCTION public.registrar_fecha_resolucion() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                IF NEW.estado_incidencia_id = (
                    SELECT id FROM estados_incidencia WHERE nombre = 'Resuelto' LIMIT 1
                ) AND (OLD.estado_incidencia_id IS DISTINCT FROM NEW.estado_incidencia_id) THEN
                    NEW.fecha_resolucion := NOW();
                END IF;
                RETURN NEW;
            END;
            $$;


ALTER FUNCTION public.registrar_fecha_resolucion() OWNER TO incidencias_user;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: asignaciones; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.asignaciones (
    id bigint NOT NULL,
    incidencia_id bigint NOT NULL,
    usuario_id bigint NOT NULL,
    fecha_asignacion timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    rol character varying(30) DEFAULT 'Responsable'::character varying NOT NULL
);


ALTER TABLE public.asignaciones OWNER TO incidencias_user;

--
-- Name: asignaciones_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.asignaciones_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.asignaciones_id_seq OWNER TO incidencias_user;

--
-- Name: asignaciones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.asignaciones_id_seq OWNED BY public.asignaciones.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache OWNER TO incidencias_user;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO incidencias_user;

--
-- Name: ciudades; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.ciudades (
    id bigint NOT NULL,
    provincia_id bigint NOT NULL,
    nombre character varying(100) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ciudades OWNER TO incidencias_user;

--
-- Name: ciudades_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.ciudades_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ciudades_id_seq OWNER TO incidencias_user;

--
-- Name: ciudades_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.ciudades_id_seq OWNED BY public.ciudades.id;


--
-- Name: comentarios; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.comentarios (
    id bigint NOT NULL,
    incidencia_id bigint NOT NULL,
    usuario_id bigint NOT NULL,
    comentario text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.comentarios OWNER TO incidencias_user;

--
-- Name: comentarios_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.comentarios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.comentarios_id_seq OWNER TO incidencias_user;

--
-- Name: comentarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.comentarios_id_seq OWNED BY public.comentarios.id;


--
-- Name: estados_incidencia; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.estados_incidencia (
    id bigint NOT NULL,
    nombre character varying(50) NOT NULL,
    color character varying(20),
    descripcion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.estados_incidencia OWNER TO incidencias_user;

--
-- Name: estados_incidencia_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.estados_incidencia_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.estados_incidencia_id_seq OWNER TO incidencias_user;

--
-- Name: estados_incidencia_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.estados_incidencia_id_seq OWNED BY public.estados_incidencia.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection character varying(255) NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO incidencias_user;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO incidencias_user;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: historial_estados; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.historial_estados (
    id bigint NOT NULL,
    incidencia_id bigint NOT NULL,
    estado_anterior_id bigint,
    estado_nuevo_id bigint NOT NULL,
    usuario_id bigint NOT NULL,
    observacion text,
    fecha_cambio timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.historial_estados OWNER TO incidencias_user;

--
-- Name: historial_estados_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.historial_estados_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.historial_estados_id_seq OWNER TO incidencias_user;

--
-- Name: historial_estados_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.historial_estados_id_seq OWNED BY public.historial_estados.id;


--
-- Name: incidencias; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.incidencias (
    id bigint NOT NULL,
    usuario_id bigint NOT NULL,
    ciudad_id bigint NOT NULL,
    tipo_incidencia_id bigint NOT NULL,
    subtipo_incidencia_id bigint NOT NULL,
    estado_incidencia_id bigint NOT NULL,
    titulo character varying(200) NOT NULL,
    descripcion text NOT NULL,
    prioridad character varying(255) DEFAULT 'Media'::character varying NOT NULL,
    latitud numeric(10,8),
    longitud numeric(11,8),
    direccion character varying(255),
    foto character varying(255),
    fecha_reporte timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    fecha_resolucion timestamp without time zone,
    CONSTRAINT incidencias_prioridad_check CHECK (((prioridad)::text = ANY ((ARRAY['Baja'::character varying, 'Media'::character varying, 'Alta'::character varying, 'Crítica'::character varying])::text[])))
);


ALTER TABLE public.incidencias OWNER TO incidencias_user;

--
-- Name: incidencias_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.incidencias_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.incidencias_id_seq OWNER TO incidencias_user;

--
-- Name: incidencias_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.incidencias_id_seq OWNED BY public.incidencias.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO incidencias_user;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO incidencias_user;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jobs_id_seq OWNER TO incidencias_user;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO incidencias_user;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO incidencias_user;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: notificaciones; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.notificaciones (
    id bigint NOT NULL,
    usuario_id bigint NOT NULL,
    titulo character varying(200) NOT NULL,
    mensaje text NOT NULL,
    leida boolean DEFAULT false NOT NULL,
    fecha_envio timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    incidencia_id bigint
);


ALTER TABLE public.notificaciones OWNER TO incidencias_user;

--
-- Name: notificaciones_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.notificaciones_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.notificaciones_id_seq OWNER TO incidencias_user;

--
-- Name: notificaciones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.notificaciones_id_seq OWNED BY public.notificaciones.id;


--
-- Name: paises; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.paises (
    id bigint NOT NULL,
    nombre character varying(100) NOT NULL,
    codigo character varying(10) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.paises OWNER TO incidencias_user;

--
-- Name: paises_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.paises_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.paises_id_seq OWNER TO incidencias_user;

--
-- Name: paises_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.paises_id_seq OWNED BY public.paises.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO incidencias_user;

--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO incidencias_user;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.personal_access_tokens_id_seq OWNER TO incidencias_user;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: provincias; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.provincias (
    id bigint NOT NULL,
    pais_id bigint NOT NULL,
    nombre character varying(100) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.provincias OWNER TO incidencias_user;

--
-- Name: provincias_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.provincias_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.provincias_id_seq OWNER TO incidencias_user;

--
-- Name: provincias_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.provincias_id_seq OWNED BY public.provincias.id;


--
-- Name: roles; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    nombre character varying(50) NOT NULL,
    descripcion character varying(200),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO incidencias_user;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.roles_id_seq OWNER TO incidencias_user;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO incidencias_user;

--
-- Name: subtipos_incidencia; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.subtipos_incidencia (
    id bigint NOT NULL,
    tipo_incidencia_id bigint NOT NULL,
    nombre character varying(100) NOT NULL,
    descripcion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.subtipos_incidencia OWNER TO incidencias_user;

--
-- Name: subtipos_incidencia_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.subtipos_incidencia_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.subtipos_incidencia_id_seq OWNER TO incidencias_user;

--
-- Name: subtipos_incidencia_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.subtipos_incidencia_id_seq OWNED BY public.subtipos_incidencia.id;


--
-- Name: tipos_incidencia; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.tipos_incidencia (
    id bigint NOT NULL,
    nombre character varying(100) NOT NULL,
    descripcion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.tipos_incidencia OWNER TO incidencias_user;

--
-- Name: tipos_incidencia_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.tipos_incidencia_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tipos_incidencia_id_seq OWNER TO incidencias_user;

--
-- Name: tipos_incidencia_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.tipos_incidencia_id_seq OWNED BY public.tipos_incidencia.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: incidencias_user
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    rol_id bigint,
    name character varying(100) NOT NULL,
    apellido character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    activo boolean DEFAULT true NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO incidencias_user;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: incidencias_user
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO incidencias_user;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: incidencias_user
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: vista_incidencias_por_estado; Type: VIEW; Schema: public; Owner: incidencias_user
--

CREATE VIEW public.vista_incidencias_por_estado AS
 SELECT e.nombre AS estado,
    count(i.id) AS total,
    round((((count(i.id))::numeric * 100.0) / (NULLIF(( SELECT count(*) AS count
           FROM public.incidencias), 0))::numeric), 1) AS porcentaje
   FROM (public.estados_incidencia e
     LEFT JOIN public.incidencias i ON ((i.estado_incidencia_id = e.id)))
  GROUP BY e.nombre
  ORDER BY (count(i.id)) DESC;


ALTER TABLE public.vista_incidencias_por_estado OWNER TO incidencias_user;

--
-- Name: vista_incidencias_por_tipo_ciudad; Type: VIEW; Schema: public; Owner: incidencias_user
--

CREATE VIEW public.vista_incidencias_por_tipo_ciudad AS
 SELECT t.nombre AS tipo,
    c.nombre AS ciudad,
    count(i.id) AS total
   FROM ((public.incidencias i
     JOIN public.tipos_incidencia t ON ((t.id = i.tipo_incidencia_id)))
     JOIN public.ciudades c ON ((c.id = i.ciudad_id)))
  GROUP BY t.nombre, c.nombre
  ORDER BY (count(i.id)) DESC;


ALTER TABLE public.vista_incidencias_por_tipo_ciudad OWNER TO incidencias_user;

--
-- Name: vista_tiempo_resolucion; Type: VIEW; Schema: public; Owner: incidencias_user
--

CREATE VIEW public.vista_tiempo_resolucion AS
 SELECT count(*) AS incidencias_resueltas,
    round(avg((EXTRACT(epoch FROM (incidencias.fecha_resolucion - incidencias.created_at)) / (3600)::numeric)), 2) AS horas_promedio,
    round(min((EXTRACT(epoch FROM (incidencias.fecha_resolucion - incidencias.created_at)) / (3600)::numeric)), 2) AS horas_minimo,
    round(max((EXTRACT(epoch FROM (incidencias.fecha_resolucion - incidencias.created_at)) / (3600)::numeric)), 2) AS horas_maximo
   FROM public.incidencias
  WHERE (incidencias.fecha_resolucion IS NOT NULL);


ALTER TABLE public.vista_tiempo_resolucion OWNER TO incidencias_user;

--
-- Name: asignaciones id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.asignaciones ALTER COLUMN id SET DEFAULT nextval('public.asignaciones_id_seq'::regclass);


--
-- Name: ciudades id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.ciudades ALTER COLUMN id SET DEFAULT nextval('public.ciudades_id_seq'::regclass);


--
-- Name: comentarios id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.comentarios ALTER COLUMN id SET DEFAULT nextval('public.comentarios_id_seq'::regclass);


--
-- Name: estados_incidencia id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.estados_incidencia ALTER COLUMN id SET DEFAULT nextval('public.estados_incidencia_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: historial_estados id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.historial_estados ALTER COLUMN id SET DEFAULT nextval('public.historial_estados_id_seq'::regclass);


--
-- Name: incidencias id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.incidencias ALTER COLUMN id SET DEFAULT nextval('public.incidencias_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: notificaciones id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.notificaciones ALTER COLUMN id SET DEFAULT nextval('public.notificaciones_id_seq'::regclass);


--
-- Name: paises id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.paises ALTER COLUMN id SET DEFAULT nextval('public.paises_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: provincias id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.provincias ALTER COLUMN id SET DEFAULT nextval('public.provincias_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: subtipos_incidencia id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.subtipos_incidencia ALTER COLUMN id SET DEFAULT nextval('public.subtipos_incidencia_id_seq'::regclass);


--
-- Name: tipos_incidencia id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.tipos_incidencia ALTER COLUMN id SET DEFAULT nextval('public.tipos_incidencia_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: asignaciones; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.asignaciones (id, incidencia_id, usuario_id, fecha_asignacion, created_at, updated_at, rol) FROM stdin;
1	6	1	2026-07-16 03:10:05	2026-07-16 03:10:04	2026-07-16 03:10:04	Responsable
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: ciudades; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.ciudades (id, provincia_id, nombre, created_at, updated_at) FROM stdin;
1	1	La Libertad	2026-07-11 01:13:17	2026-07-11 01:13:17
2	1	Santa Elena	2026-07-11 01:13:17	2026-07-11 01:13:17
3	1	Salinas	2026-07-11 01:13:17	2026-07-11 01:13:17
\.


--
-- Data for Name: comentarios; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.comentarios (id, incidencia_id, usuario_id, comentario, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: estados_incidencia; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.estados_incidencia (id, nombre, color, descripcion, created_at, updated_at) FROM stdin;
1	Pendiente	warning	Incidencia registrada, pendiente de atención	2026-07-11 01:13:17	2026-07-11 01:13:17
2	En Proceso	info	Incidencia siendo atendida	2026-07-11 01:13:17	2026-07-11 01:13:17
3	Resuelto	success	Incidencia resuelta satisfactoriamente	2026-07-11 01:13:17	2026-07-11 01:13:17
4	Rechazado	danger	Incidencia rechazada por no cumplir criterios	2026-07-11 01:13:17	2026-07-11 01:13:17
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: historial_estados; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.historial_estados (id, incidencia_id, estado_anterior_id, estado_nuevo_id, usuario_id, observacion, fecha_cambio, created_at, updated_at) FROM stdin;
1	1	\N	1	4	Incidencia registrada	2026-07-14 20:42:01	2026-07-14 20:42:00	2026-07-14 20:42:00
6	6	\N	1	9	Incidencia registrada	2026-07-16 03:06:31	2026-07-16 03:06:31	2026-07-16 03:06:31
7	6	1	3	1	Se realizo la limpieza necesaria	2026-07-16 03:09:50	2026-07-16 03:09:50	2026-07-16 03:09:50
\.


--
-- Data for Name: incidencias; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.incidencias (id, usuario_id, ciudad_id, tipo_incidencia_id, subtipo_incidencia_id, estado_incidencia_id, titulo, descripcion, prioridad, latitud, longitud, direccion, foto, fecha_reporte, created_at, updated_at, fecha_resolucion) FROM stdin;
1	4	3	1	2	1	Bache en la calle principal	Calle destruida	Media	-2.20790000	-80.96720000	Avenida de julio	\N	2026-07-14 20:42:01	2026-07-14 20:42:00	2026-07-14 20:42:00	\N
6	9	1	3	10	3	Calle contaminada	La calle de la avenida principal se encuentra llena de basura y esta contaminando el ambiente	Media	-2.22865300	-80.92587200	Avenida Carlos Espinoza	\N	2026-07-16 03:06:31	2026-07-16 03:06:31	2026-07-16 03:09:50	2026-07-16 03:09:50.296451
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_06_19_035654_create_roles_table	1
5	2026_06_19_035711_create_paises_table	1
6	2026_06_19_035747_create_provincias_table	1
7	2026_06_19_035908_create_ciudades_table	1
8	2026_06_19_035927_create_tipos_incidencia_table	1
9	2026_06_19_040004_create_subtipos_incidencia_table	1
10	2026_06_19_040040_create_estados_incidencia_table	1
11	2026_06_19_040041_create_incidencias_table	1
12	2026_06_19_040253_create_historial_estados_table	1
13	2026_06_19_040311_create_asignaciones_table	1
14	2026_06_19_040333_create_comentarios_table	1
15	2026_06_19_040345_create_notificaciones_table	1
16	2026_06_20_002617_add_rol_foreign_to_users_table	2
17	2026_06_20_025419_create_personal_access_tokens_table	2
18	2026_07_14_221422_add_incidencia_id_to_notificaciones_table	3
19	2026_07_15_031134_add_columnas_faltantes_to_asignaciones_table	3
20	2026_07_15_195224_crear_vistas_y_trigger_sql	3
\.


--
-- Data for Name: notificaciones; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.notificaciones (id, usuario_id, titulo, mensaje, leida, fecha_envio, created_at, updated_at, incidencia_id) FROM stdin;
1	1	Nueva incidencia #6	Se registró la incidencia "Calle contaminada" con prioridad Media.	t	2026-07-16 03:06:31	2026-07-16 03:06:31	2026-07-16 03:08:58	6
2	9	Cambio de estado en incidencia #6	La incidencia "Calle contaminada" cambió de "Pendiente" a "Resuelto".	f	2026-07-16 03:09:50	2026-07-16 03:09:50	2026-07-16 03:09:50	6
3	1	Nueva asignación en incidencia #6	Fuiste asignado como Responsable en la incidencia "Calle contaminada".	t	2026-07-16 03:10:05	2026-07-16 03:10:04	2026-07-16 03:10:35	6
\.


--
-- Data for Name: paises; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.paises (id, nombre, codigo, created_at, updated_at) FROM stdin;
1	Ecuador	EC	2026-07-11 01:13:17	2026-07-11 01:13:17
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
1	App\\Models\\User	1	auth_token	2937a7bd52c7ba3720aee954f583e91dbb75491a3207aec4e0392df65f55cd8f	["*"]	2026-07-10 05:23:50	\N	2026-07-10 05:18:18	2026-07-10 05:23:50
9	App\\Models\\User	1	auth_token	b6aedb01e87d334e9123fef772fa2c183f83f15d21865add20364420eb97b7aa	["*"]	2026-07-13 02:05:04	\N	2026-07-13 01:59:36	2026-07-13 02:05:04
11	App\\Models\\User	1	auth_token	fabba7362eb3bb92853664291b00c576d58f283876c791b04c39eda3ec907779	["*"]	\N	\N	2026-07-13 15:16:52	2026-07-13 15:16:52
13	App\\Models\\User	1	auth_token	e16a3d5a8709e534437086dd45c32f3c0a9230192fcd73fa479d6f9abef1d4f0	["*"]	\N	\N	2026-07-13 15:16:54	2026-07-13 15:16:54
15	App\\Models\\User	1	auth_token	6bb32f15685065b5feea8ca538303e2eb47e948847ff9305eebe3e4d12933bf1	["*"]	\N	\N	2026-07-13 19:57:21	2026-07-13 19:57:21
21	App\\Models\\User	1	auth_token	5c8a1b36c250692f52b1f105d8e7759721719815eaf45919fa53941c7e39ea94	["*"]	2026-07-16 04:01:32	\N	2026-07-16 03:08:08	2026-07-16 04:01:32
5	App\\Models\\User	1	auth_token	12512df36d7f863c78cd8ea7713d82e8d845d1899a625dbe8a34c36731c3f2e9	["*"]	\N	\N	2026-07-11 01:49:58	2026-07-11 01:49:58
18	App\\Models\\User	4	auth_token	2c5f1f365b55c4cd1e4925ddf22c82e2085cd9708840debe47982e64b526b107	["*"]	\N	\N	2026-07-14 20:32:05	2026-07-14 20:32:05
\.


--
-- Data for Name: provincias; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.provincias (id, pais_id, nombre, created_at, updated_at) FROM stdin;
1	1	Santa Elena	2026-07-11 01:13:17	2026-07-11 01:13:17
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.roles (id, nombre, descripcion, created_at, updated_at) FROM stdin;
2	Administrador	Acceso total al sistema	2026-07-11 01:13:17	2026-07-11 01:13:17
3	Responsable	Gestiona y atiende incidencias asignadas	2026-07-11 01:13:17	2026-07-11 01:13:17
4	Ciudadano	Registra y consulta incidencias	2026-07-11 01:13:17	2026-07-11 01:13:17
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: subtipos_incidencia; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.subtipos_incidencia (id, tipo_incidencia_id, nombre, descripcion, created_at, updated_at) FROM stdin;
1	1	Alumbrado público	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
2	1	Bache en vía	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
3	1	Semáforo dañado	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
4	1	Alcantarillado	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
5	1	Vía en mal estado	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
6	2	Robo	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
7	2	Vandalismo	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
8	2	Riña / Altercado	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
9	2	Punto de venta de drogas	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
10	3	Acumulación de basura	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
11	3	Contaminación de agua	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
12	3	Tala ilegal	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
13	3	Quema no autorizada	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
14	4	Corte de agua potable	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
15	4	Corte de energía eléctrica	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
16	4	Fuga de agua	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
17	5	Foco de plagas	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
18	5	Animal en la vía pública	\N	2026-07-11 01:13:17	2026-07-11 01:13:17
\.


--
-- Data for Name: tipos_incidencia; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.tipos_incidencia (id, nombre, descripcion, created_at, updated_at) FROM stdin;
1	Infraestructura	Incidencias relacionadas con Infraestructura	2026-07-11 01:13:17	2026-07-11 01:13:17
2	Seguridad	Incidencias relacionadas con Seguridad	2026-07-11 01:13:17	2026-07-11 01:13:17
3	Ambiental	Incidencias relacionadas con Ambiental	2026-07-11 01:13:17	2026-07-11 01:13:17
4	Servicios Básicos	Incidencias relacionadas con Servicios Básicos	2026-07-11 01:13:17	2026-07-11 01:13:17
5	Salud Pública	Incidencias relacionadas con Salud Pública	2026-07-11 01:13:17	2026-07-11 01:13:17
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.users (id, rol_id, name, apellido, email, email_verified_at, password, activo, remember_token, created_at, updated_at) FROM stdin;
2	3	Kerlly	Mite	kerllymite@gmail.com	\N	$2y$12$kZUVAkfhZTUW3wlDlSnAj.5EeLFsJklD7qFAly5tfZ5N.IG6VmRrK	f	\N	2026-07-10 23:08:03	2026-07-11 01:59:59
1	2	Admin	Sistema	admin@incidencias.com	\N	$2y$12$63wdkU1AETg/y8s9BeL.zuW3emTlYgOwh4CYYic8zxuYJQm4BGBQO	t	\N	2026-07-10 03:57:45	2026-07-13 01:57:44
4	4	Skay	Alvarado	skay@gmail.com	\N	$2y$12$wpp7cVo.U9nnV.myPfRmzelB39/H2MKvUna7ONBq3yjhzBNvtUwRi	t	\N	2026-07-11 01:56:44	2026-07-13 02:12:03
9	3	Kerlly	mite	kerlly@gmail.com	\N	$2y$12$hGd2dpea0wf1YI56i9FhSOW5cLdsBRM4jOaHdDSwV/5g6BCC9PQoi	t	\N	2026-07-16 02:44:10	2026-07-16 02:44:10
\.


--
-- Name: asignaciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.asignaciones_id_seq', 1, true);


--
-- Name: ciudades_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.ciudades_id_seq', 3, true);


--
-- Name: comentarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.comentarios_id_seq', 1, false);


--
-- Name: estados_incidencia_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.estados_incidencia_id_seq', 4, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: historial_estados_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.historial_estados_id_seq', 7, true);


--
-- Name: incidencias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.incidencias_id_seq', 6, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.migrations_id_seq', 20, true);


--
-- Name: notificaciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.notificaciones_id_seq', 3, true);


--
-- Name: paises_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.paises_id_seq', 1, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 21, true);


--
-- Name: provincias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.provincias_id_seq', 1, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.roles_id_seq', 4, true);


--
-- Name: subtipos_incidencia_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.subtipos_incidencia_id_seq', 18, true);


--
-- Name: tipos_incidencia_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.tipos_incidencia_id_seq', 5, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.users_id_seq', 9, true);


--
-- Name: asignaciones asignaciones_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.asignaciones
    ADD CONSTRAINT asignaciones_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: ciudades ciudades_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.ciudades
    ADD CONSTRAINT ciudades_pkey PRIMARY KEY (id);


--
-- Name: ciudades ciudades_provincia_id_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.ciudades
    ADD CONSTRAINT ciudades_provincia_id_nombre_unique UNIQUE (provincia_id, nombre);


--
-- Name: comentarios comentarios_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.comentarios
    ADD CONSTRAINT comentarios_pkey PRIMARY KEY (id);


--
-- Name: estados_incidencia estados_incidencia_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.estados_incidencia
    ADD CONSTRAINT estados_incidencia_nombre_unique UNIQUE (nombre);


--
-- Name: estados_incidencia estados_incidencia_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.estados_incidencia
    ADD CONSTRAINT estados_incidencia_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: historial_estados historial_estados_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.historial_estados
    ADD CONSTRAINT historial_estados_pkey PRIMARY KEY (id);


--
-- Name: incidencias incidencias_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.incidencias
    ADD CONSTRAINT incidencias_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: notificaciones notificaciones_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.notificaciones
    ADD CONSTRAINT notificaciones_pkey PRIMARY KEY (id);


--
-- Name: paises paises_codigo_unique; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.paises
    ADD CONSTRAINT paises_codigo_unique UNIQUE (codigo);


--
-- Name: paises paises_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.paises
    ADD CONSTRAINT paises_nombre_unique UNIQUE (nombre);


--
-- Name: paises paises_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.paises
    ADD CONSTRAINT paises_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: provincias provincias_pais_id_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.provincias
    ADD CONSTRAINT provincias_pais_id_nombre_unique UNIQUE (pais_id, nombre);


--
-- Name: provincias provincias_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.provincias
    ADD CONSTRAINT provincias_pkey PRIMARY KEY (id);


--
-- Name: roles roles_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_nombre_unique UNIQUE (nombre);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: subtipos_incidencia subtipos_incidencia_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.subtipos_incidencia
    ADD CONSTRAINT subtipos_incidencia_pkey PRIMARY KEY (id);


--
-- Name: subtipos_incidencia subtipos_incidencia_tipo_incidencia_id_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.subtipos_incidencia
    ADD CONSTRAINT subtipos_incidencia_tipo_incidencia_id_nombre_unique UNIQUE (tipo_incidencia_id, nombre);


--
-- Name: tipos_incidencia tipos_incidencia_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.tipos_incidencia
    ADD CONSTRAINT tipos_incidencia_nombre_unique UNIQUE (nombre);


--
-- Name: tipos_incidencia tipos_incidencia_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.tipos_incidencia
    ADD CONSTRAINT tipos_incidencia_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: incidencias_user
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: incidencias_user
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: failed_jobs_connection_queue_failed_at_index; Type: INDEX; Schema: public; Owner: incidencias_user
--

CREATE INDEX failed_jobs_connection_queue_failed_at_index ON public.failed_jobs USING btree (connection, queue, failed_at);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: incidencias_user
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: incidencias_user
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: incidencias_user
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: incidencias_user
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: incidencias_user
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: incidencias trg_fecha_resolucion; Type: TRIGGER; Schema: public; Owner: incidencias_user
--

CREATE TRIGGER trg_fecha_resolucion BEFORE UPDATE ON public.incidencias FOR EACH ROW EXECUTE FUNCTION public.registrar_fecha_resolucion();


--
-- Name: asignaciones asignaciones_incidencia_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.asignaciones
    ADD CONSTRAINT asignaciones_incidencia_id_foreign FOREIGN KEY (incidencia_id) REFERENCES public.incidencias(id) ON DELETE CASCADE;


--
-- Name: asignaciones asignaciones_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.asignaciones
    ADD CONSTRAINT asignaciones_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: ciudades ciudades_provincia_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.ciudades
    ADD CONSTRAINT ciudades_provincia_id_foreign FOREIGN KEY (provincia_id) REFERENCES public.provincias(id) ON DELETE CASCADE;


--
-- Name: comentarios comentarios_incidencia_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.comentarios
    ADD CONSTRAINT comentarios_incidencia_id_foreign FOREIGN KEY (incidencia_id) REFERENCES public.incidencias(id) ON DELETE CASCADE;


--
-- Name: comentarios comentarios_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.comentarios
    ADD CONSTRAINT comentarios_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: historial_estados historial_estados_estado_anterior_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.historial_estados
    ADD CONSTRAINT historial_estados_estado_anterior_id_foreign FOREIGN KEY (estado_anterior_id) REFERENCES public.estados_incidencia(id) ON DELETE SET NULL;


--
-- Name: historial_estados historial_estados_estado_nuevo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.historial_estados
    ADD CONSTRAINT historial_estados_estado_nuevo_id_foreign FOREIGN KEY (estado_nuevo_id) REFERENCES public.estados_incidencia(id) ON DELETE RESTRICT;


--
-- Name: historial_estados historial_estados_incidencia_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.historial_estados
    ADD CONSTRAINT historial_estados_incidencia_id_foreign FOREIGN KEY (incidencia_id) REFERENCES public.incidencias(id) ON DELETE CASCADE;


--
-- Name: historial_estados historial_estados_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.historial_estados
    ADD CONSTRAINT historial_estados_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: incidencias incidencias_ciudad_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.incidencias
    ADD CONSTRAINT incidencias_ciudad_id_foreign FOREIGN KEY (ciudad_id) REFERENCES public.ciudades(id) ON DELETE RESTRICT;


--
-- Name: incidencias incidencias_estado_incidencia_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.incidencias
    ADD CONSTRAINT incidencias_estado_incidencia_id_foreign FOREIGN KEY (estado_incidencia_id) REFERENCES public.estados_incidencia(id) ON DELETE RESTRICT;


--
-- Name: incidencias incidencias_subtipo_incidencia_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.incidencias
    ADD CONSTRAINT incidencias_subtipo_incidencia_id_foreign FOREIGN KEY (subtipo_incidencia_id) REFERENCES public.subtipos_incidencia(id) ON DELETE RESTRICT;


--
-- Name: incidencias incidencias_tipo_incidencia_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.incidencias
    ADD CONSTRAINT incidencias_tipo_incidencia_id_foreign FOREIGN KEY (tipo_incidencia_id) REFERENCES public.tipos_incidencia(id) ON DELETE RESTRICT;


--
-- Name: incidencias incidencias_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.incidencias
    ADD CONSTRAINT incidencias_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: notificaciones notificaciones_incidencia_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.notificaciones
    ADD CONSTRAINT notificaciones_incidencia_id_foreign FOREIGN KEY (incidencia_id) REFERENCES public.incidencias(id) ON DELETE CASCADE;


--
-- Name: notificaciones notificaciones_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.notificaciones
    ADD CONSTRAINT notificaciones_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: provincias provincias_pais_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.provincias
    ADD CONSTRAINT provincias_pais_id_foreign FOREIGN KEY (pais_id) REFERENCES public.paises(id) ON DELETE CASCADE;


--
-- Name: subtipos_incidencia subtipos_incidencia_tipo_incidencia_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.subtipos_incidencia
    ADD CONSTRAINT subtipos_incidencia_tipo_incidencia_id_foreign FOREIGN KEY (tipo_incidencia_id) REFERENCES public.tipos_incidencia(id) ON DELETE CASCADE;


--
-- Name: users users_rol_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: incidencias_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_rol_id_foreign FOREIGN KEY (rol_id) REFERENCES public.roles(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict up4nyjkYZnRwFf56TUu2FaLIQrCxracjIfOrhuCgEiBY5lYvIhsVKw0KxPNHCa4

