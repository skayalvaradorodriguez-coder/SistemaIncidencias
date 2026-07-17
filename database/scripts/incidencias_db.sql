--
-- PostgreSQL database dump
--

\restrict d1lCH9sUldQ3FF1iKnYAqnfbeQmrll8WJm8mNs3YQ3GT8deHrf2JHwKpm0QOq93

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
    CONSTRAINT incidencias_prioridad_check CHECK (((prioridad)::text = ANY (ARRAY[('Baja'::character varying)::text, ('Media'::character varying)::text, ('Alta'::character varying)::text, ('Crítica'::character varying)::text])))
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
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    rol_id bigint,
    apellido character varying(100) NOT NULL,
    activo boolean DEFAULT true NOT NULL
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
1	39	2	2026-07-15 03:14:54	2026-07-15 03:14:54	2026-07-15 03:14:54	Apoyo
3	39	1	2026-07-15 03:56:49	2026-07-15 03:56:48	2026-07-15 03:56:48	Responsable
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.cache (key, value, expiration) FROM stdin;
laravel-cache-748d8fb071be83587f9ba1027754a83167b697b3:timer	i:1784171318;	1784171318
laravel-cache-748d8fb071be83587f9ba1027754a83167b697b3	i:1;	1784171318
laravel-cache-b7ad7f2b04bd98f199a2b8c016e37e66c831b866:timer	i:1784264145;	1784264145
laravel-cache-b7ad7f2b04bd98f199a2b8c016e37e66c831b866	i:2;	1784264145
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
1	1	La Libertad	2026-07-16 02:41:55	2026-07-16 02:41:55
2	1	Santa Elena	2026-07-16 02:41:55	2026-07-16 02:41:55
3	1	Salinas	2026-07-16 02:41:55	2026-07-16 02:41:55
\.


--
-- Data for Name: comentarios; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.comentarios (id, incidencia_id, usuario_id, comentario, created_at, updated_at) FROM stdin;
1	20	1	Se notificó al departamento de obras públicas para atender la incidencia.	2026-07-08 05:53:16	2026-07-08 05:53:16
2	39	1	la incidencia esta en proceso con la ayuda de Skay	2026-07-15 03:43:57	2026-07-15 03:43:57
3	39	2	ya se completo	2026-07-15 03:57:11	2026-07-15 03:57:11
4	39	1	ok	2026-07-15 04:38:27	2026-07-15 04:38:27
\.


--
-- Data for Name: estados_incidencia; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.estados_incidencia (id, nombre, color, descripcion, created_at, updated_at) FROM stdin;
1	Pendiente	warning	Incidencia registrada, pendiente de atención	2026-07-16 02:41:55	2026-07-16 02:41:55
2	En Proceso	info	Incidencia siendo atendida	2026-07-16 02:41:55	2026-07-16 02:41:55
3	Resuelto	success	Incidencia resuelta satisfactoriamente	2026-07-16 02:41:55	2026-07-16 02:41:55
4	Rechazado	danger	Incidencia rechazada por no cumplir criterios	2026-07-16 02:41:55	2026-07-16 02:41:55
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
2	1	\N	1	1	Incidencia registrada	2026-07-06 05:39:01	2026-07-06 05:39:00	2026-07-06 05:39:00
37	34	\N	1	1	Incidencia registrada	2026-07-14 22:10:56	2026-07-14 22:10:55	2026-07-14 22:10:55
40	37	\N	1	1	Incidencia registrada	2026-07-14 22:11:57	2026-07-14 22:11:56	2026-07-14 22:11:56
41	38	\N	1	1	Incidencia registrada	2026-07-14 22:18:37	2026-07-14 22:18:37	2026-07-14 22:18:37
42	39	\N	1	1	Incidencia registrada	2026-07-14 22:18:37	2026-07-14 22:18:37	2026-07-14 22:18:37
43	39	1	2	1	Cambio de estado	2026-07-15 03:43:17	2026-07-15 03:43:16	2026-07-15 03:43:16
44	39	2	3	2	Cambio de estado	2026-07-15 03:56:42	2026-07-15 03:56:41	2026-07-15 03:56:41
17	17	\N	1	1	Incidencia registrada	2026-07-06 06:41:51	2026-07-06 06:41:50	2026-07-06 06:41:50
23	20	2	3	1	se completo	2026-07-08 05:24:11	2026-07-08 05:24:11	2026-07-08 05:24:11
28	25	\N	1	1	Incidencia registrada	2026-07-11 01:38:27	2026-07-11 01:38:26	2026-07-11 01:38:26
30	27	\N	1	1	Incidencia registrada	2026-07-14 22:04:24	2026-07-14 22:04:23	2026-07-14 22:04:23
45	17	1	3	1	culmino	2026-07-15 19:59:35	2026-07-15 19:59:35	2026-07-15 19:59:35
31	28	\N	1	1	Incidencia registrada	2026-07-14 22:04:24	2026-07-14 22:04:23	2026-07-14 22:04:23
20	20	\N	1	1	Incidencia registrada	2026-07-07 03:34:06	2026-07-07 03:34:05	2026-07-07 03:34:05
32	29	\N	1	1	Incidencia registrada	2026-07-14 22:05:23	2026-07-14 22:05:22	2026-07-14 22:05:22
21	20	1	1	1	Cambio de estado	2026-07-07 07:18:57	2026-07-07 07:18:57	2026-07-07 07:18:57
33	31	\N	1	1	Incidencia registrada	2026-07-14 22:05:36	2026-07-14 22:05:35	2026-07-14 22:05:35
22	20	1	2	1	Se inició la revisión de la incidencia.	2026-07-07 07:20:29	2026-07-07 07:20:28	2026-07-07 07:20:28
34	30	\N	1	1	Incidencia registrada	2026-07-14 22:05:36	2026-07-14 22:05:35	2026-07-14 22:05:35
35	32	\N	1	1	Incidencia registrada	2026-07-14 22:05:36	2026-07-14 22:05:35	2026-07-14 22:05:35
36	33	\N	1	1	Incidencia registrada	2026-07-14 22:10:41	2026-07-14 22:10:40	2026-07-14 22:10:40
1	2	\N	1	1	Incidencia registrada	2026-07-06 05:39:01	2026-07-06 05:39:00	2026-07-06 05:39:00
\.


--
-- Data for Name: incidencias; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.incidencias (id, usuario_id, ciudad_id, tipo_incidencia_id, subtipo_incidencia_id, estado_incidencia_id, titulo, descripcion, prioridad, latitud, longitud, direccion, foto, fecha_reporte, created_at, updated_at, fecha_resolucion) FROM stdin;
1	1	1	1	2	1	bache en avenida principal	bache en avenida principal de la 24 de mayo donde ocurren accidentes	Crítica	-2.22081170	-80.90776470	Av.31 entre calle 17 y 18_ 24 de Mayo	\N	2026-07-06 05:39:01	2026-07-06 05:39:00	2026-07-06 05:39:00	\N
25	1	2	1	5	1	bache en avenida principal	accidentes	Alta	-2.22081170	-80.90776470	28 de mayo	\N	2026-07-11 01:38:27	2026-07-11 01:38:26	2026-07-11 01:38:26	\N
27	1	1	1	1	1	Poste de alumbrado público dañado en Av. Eleodoro Solórzano	El poste de luz ubicado frente al parque central lleva más de una semana sin funcionar, dejando la zona completamente oscura en la noche, lo que genera inseguridad para los peatones.	Alta	-2.29429300	-80.68848200	\N	\N	2026-07-14 22:04:24	2026-07-14 22:04:23	2026-07-14 22:04:23	\N
17	1	3	1	3	3	semaforo dañado	robos	Alta	-2.22081170	-80.90776470	chipipe	\N	2026-07-06 06:41:51	2026-07-06 06:41:50	2026-07-15 19:59:35	2026-07-15 19:59:35.019128
28	1	1	1	1	1	Poste de alumbrado público dañado en Av. Eleodoro Solórzano	El poste de luz ubicado frente al parque central lleva más de una semana sin funcionar, dejando la zona completamente oscura en la noche, lo que genera inseguridad para los peatones.	Alta	-2.29429300	-80.68848200	\N	\N	2026-07-14 22:04:24	2026-07-14 22:04:23	2026-07-14 22:04:23	\N
20	1	1	1	2	3	Bache en avenida principal	Existe un bache grande que dificulta el tránsito vehicular.	Alta	-2.22900000	-80.85760000	Av. principal, sector centro	\N	2026-07-07 03:34:06	2026-07-07 03:34:05	2026-07-08 05:24:11	\N
29	1	1	1	1	1	Poste de alumbrado público dañado en Av. Eleodoro Solórzano	El poste de luz ubicado frente al parque central lleva más de una semana sin funcionar, dejando la zona completamente oscura en la noche, lo que genera inseguridad para los peatones.	Alta	-2.29429300	-80.68848200	\N	\N	2026-07-14 22:05:22	2026-07-14 22:05:22	2026-07-14 22:05:22	\N
30	1	1	1	1	1	Poste de alumbrado público dañado en Av. Eleodoro Solórzano	El poste de luz ubicado frente al parque central lleva más de una semana sin funcionar, dejando la zona completamente oscura en la noche, lo que genera inseguridad para los peatones.	Alta	-2.29429300	-80.68848200	\N	\N	2026-07-14 22:05:36	2026-07-14 22:05:35	2026-07-14 22:05:35	\N
31	1	1	1	1	1	Poste de alumbrado público dañado en Av. Eleodoro Solórzano	El poste de luz ubicado frente al parque central lleva más de una semana sin funcionar, dejando la zona completamente oscura en la noche, lo que genera inseguridad para los peatones.	Alta	-2.29429300	-80.68848200	\N	\N	2026-07-14 22:05:36	2026-07-14 22:05:35	2026-07-14 22:05:35	\N
32	1	1	1	1	1	Poste de alumbrado público dañado en Av. Eleodoro Solórzano	El poste de luz ubicado frente al parque central lleva más de una semana sin funcionar, dejando la zona completamente oscura en la noche, lo que genera inseguridad para los peatones.	Alta	-2.29429300	-80.68848200	\N	\N	2026-07-14 22:05:36	2026-07-14 22:05:35	2026-07-14 22:05:35	\N
33	1	1	1	1	1	Poste de alumbrado público dañado en Av. Eleodoro Solórzano	El poste de luz ubicado frente al parque central lleva más de una semana sin funcionar, dejando la zona completamente oscura en la noche, lo que genera inseguridad para los peatones.	Alta	-2.29429300	-80.68848200	\N	\N	2026-07-14 22:10:41	2026-07-14 22:10:40	2026-07-14 22:10:40	\N
34	1	1	1	1	1	Poste de alumbrado público dañado en Av. Eleodoro Solórzano	El poste de luz ubicado frente al parque central lleva más de una semana sin funcionar, dejando la zona completamente oscura en la noche, lo que genera inseguridad para los peatones.	Alta	-2.26575100	-80.71110800	\N	\N	2026-07-14 22:10:56	2026-07-14 22:10:55	2026-07-14 22:10:55	\N
37	1	1	1	1	1	Poste de alumbrado público dañado en Av. Eleodoro Solórzano	El poste de luz ubicado frente al parque central lleva más de una semana sin funcionar, dejando la zona completamente oscura en la noche, lo que genera inseguridad para los peatones.	Alta	-2.26575100	-80.71110800	\N	\N	2026-07-14 22:11:57	2026-07-14 22:11:56	2026-07-14 22:11:56	\N
38	1	1	1	1	1	Poste de alumbrado público dañado en Av. Eleodoro Solórzano	El poste de luz ubicado frente al parque central lleva más de una semana sin funcionar, dejando la zona completamente oscura en la noche, lo que genera inseguridad para los peatones.	Alta	-2.26575100	-80.71110800	\N	\N	2026-07-14 22:18:37	2026-07-14 22:18:36	2026-07-14 22:18:36	\N
39	1	1	1	1	3	Poste de alumbrado público dañado en Av. Eleodoro Solórzano	El poste de luz ubicado frente al parque central lleva más de una semana sin funcionar, dejando la zona completamente oscura en la noche, lo que genera inseguridad para los peatones.	Alta	-2.26575100	-80.71110800	\N	\N	2026-07-14 22:18:37	2026-07-14 22:18:36	2026-07-15 03:56:41	\N
2	1	1	1	2	1	bache en avenida principal	bache en avenida principal del barrio Velasco Ibarra donde ocurren accidentes	Crítica	-2.22081170	-80.90776470	Velasco Ibarra	\N	2026-07-06 05:39:01	2026-07-06 05:39:00	2026-07-07 04:59:32	\N
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
4	2026_06_19_035654_create_roles_table	2
5	2026_06_19_035711_create_paises_table	2
6	2026_06_19_035747_create_provincias_table	2
7	2026_06_19_035908_create_ciudades_table	2
8	2026_06_19_035927_create_tipos_incidencia_table	2
9	2026_06_19_040004_create_subtipos_incidencia_table	2
10	2026_06_19_040040_create_estados_incidencia_table	3
11	2026_06_19_040041_create_incidencias_table	3
12	2026_06_19_040253_create_historial_estados_table	3
13	2026_06_19_040311_create_asignaciones_table	3
14	2026_06_19_040333_create_comentarios_table	3
15	2026_06_19_040345_create_notificaciones_table	3
16	2026_06_20_002617_add_rol_foreign_to_users_table	4
17	2026_06_20_025419_create_personal_access_tokens_table	5
18	2026_07_14_221422_add_incidencia_id_to_notificaciones_table	6
19	2026_07_15_031134_add_columnas_faltantes_to_asignaciones_table	7
20	2026_07_15_195224_crear_vistas_y_trigger_sql	8
21	2026_07_16_171607_add_foto_to_incidencias_table	9
\.


--
-- Data for Name: notificaciones; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.notificaciones (id, usuario_id, titulo, mensaje, leida, fecha_envio, created_at, updated_at, incidencia_id) FROM stdin;
7	1	Cambio de estado en incidencia #39	La incidencia "Poste de alumbrado público dañado en Av. Eleodoro Solórzano" cambió de "En Proceso" a "Resuelto".	t	2026-07-15 03:56:42	2026-07-15 03:56:42	2026-07-15 04:12:30	39
9	1	Nuevo comentario en incidencia #39	Se agregó un nuevo comentario en la incidencia "Poste de alumbrado público dañado en Av. Eleodoro Solórzano".	t	2026-07-15 03:57:11	2026-07-15 03:57:11	2026-07-15 04:12:36	39
8	1	Nueva asignación en incidencia #39	Fuiste asignado como Responsable en la incidencia "Poste de alumbrado público dañado en Av. Eleodoro Solórzano".	t	2026-07-15 03:56:49	2026-07-15 03:56:48	2026-07-15 04:12:36	39
1	2	Nueva incidencia #38	Se registró la incidencia "Poste de alumbrado público dañado en Av. Eleodoro Solórzano" con prioridad Alta.	t	2026-07-14 22:18:37	2026-07-14 22:18:37	2026-07-15 04:18:42	38
2	2	Nueva incidencia #39	Se registró la incidencia "Poste de alumbrado público dañado en Av. Eleodoro Solórzano" con prioridad Alta.	t	2026-07-14 22:18:37	2026-07-14 22:18:37	2026-07-15 04:18:42	39
3	2	Nueva asignación en incidencia #39	Fuiste asignado como Apoyo en la incidencia "Poste de alumbrado público dañado en Av. Eleodoro Solórzano".	t	2026-07-15 03:14:54	2026-07-15 03:14:54	2026-07-15 04:18:42	39
4	2	Nueva asignación en incidencia #39	Fuiste asignado como Apoyo en la incidencia "Poste de alumbrado público dañado en Av. Eleodoro Solórzano".	t	2026-07-15 03:14:54	2026-07-15 03:14:54	2026-07-15 04:18:42	39
5	2	Cambio de estado en incidencia #39	La incidencia "Poste de alumbrado público dañado en Av. Eleodoro Solórzano" cambió de "Pendiente" a "En Proceso".	t	2026-07-15 03:43:17	2026-07-15 03:43:17	2026-07-15 04:18:42	39
6	2	Nuevo comentario en incidencia #39	Se agregó un nuevo comentario en la incidencia "Poste de alumbrado público dañado en Av. Eleodoro Solórzano".	t	2026-07-15 03:43:58	2026-07-15 03:43:57	2026-07-15 04:18:42	39
10	2	Nuevo comentario en incidencia #39	Se agregó un nuevo comentario en la incidencia "Poste de alumbrado público dañado en Av. Eleodoro Solórzano".	t	2026-07-15 04:38:28	2026-07-15 04:38:27	2026-07-15 05:57:20	39
\.


--
-- Data for Name: paises; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.paises (id, nombre, codigo, created_at, updated_at) FROM stdin;
1	Ecuador	EC	2026-07-16 02:41:55	2026-07-16 02:41:55
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
1	App\\Models\\User	1	auth_token	208f19da6e41fb402861474564cb4465e4dec608f510dba8349f4bc63b3c33f8	["*"]	\N	\N	2026-06-20 03:04:14	2026-06-20 03:04:14
47	App\\Models\\User	1	auth_token	9056383554160c1b2c51a942e12b06254f5d8bb6337f401f092f50c57f426df5	["*"]	\N	\N	2026-07-15 03:47:52	2026-07-15 03:47:52
37	App\\Models\\User	1	auth_token	a7ad8dbb678009f947980500cff6154365c0bab123985932905df8e65e10387a	["*"]	\N	\N	2026-07-13 19:00:26	2026-07-13 19:00:26
38	App\\Models\\User	1	auth_token	8755f782bb637b227de4948d3907e26774e626d7dc5257f4c42c4bb2dfa0ffad	["*"]	\N	\N	2026-07-13 19:00:54	2026-07-13 19:00:54
3	App\\Models\\User	1	auth_token	1d92894a76127962f4f3ed1e0b053b55a8fd0eb9cc320dd92f90fc3da496d880	["*"]	2026-06-22 21:41:54	\N	2026-06-20 03:58:37	2026-06-22 21:41:54
4	App\\Models\\User	1	auth_token	ff9f2105188a71f3968825ac752cbe28129a123c56a29e66b72c1e94ffde1ef9	["*"]	\N	\N	2026-06-29 21:03:50	2026-06-29 21:03:50
5	App\\Models\\User	1	auth_token	1090ffd421e1bab47938df563d9850893331588dc09d45c51aa466519d6f3e5d	["*"]	\N	\N	2026-06-29 21:07:58	2026-06-29 21:07:58
8	App\\Models\\User	1	auth_token	554feeda3f088b46fc7102c005ba3114d1b61db7ea08fa0002ce471735d7838a	["*"]	\N	\N	2026-07-01 06:46:11	2026-07-01 06:46:11
6	App\\Models\\User	1	auth_token	f9728243927f01338ddd7bc7797461604cefb79ae06380e414f3a7355ec8fd68	["*"]	\N	\N	2026-07-01 06:46:11	2026-07-01 06:46:11
7	App\\Models\\User	1	auth_token	0e0d2716c86b13b6e7ec891f85fbab95c3beabc14cb6aa091e814110005cacb4	["*"]	\N	\N	2026-07-01 06:46:11	2026-07-01 06:46:11
9	App\\Models\\User	1	auth_token	0165bc64a5a8cec68341a992911834ec28914aa7f8b4f3e0c0b47c9dca2b0de2	["*"]	\N	\N	2026-07-01 06:46:11	2026-07-01 06:46:11
11	App\\Models\\User	1	auth_token	ef6af057c56f38e490b063cdf6b3284c3a2576595629ecf4510fb1ba5928082a	["*"]	\N	\N	2026-07-01 06:46:14	2026-07-01 06:46:14
12	App\\Models\\User	1	auth_token	3259c5b8daacf16429319f609e45579ccd498289ac89712982419092ebdbbb56	["*"]	\N	\N	2026-07-01 06:46:14	2026-07-01 06:46:14
10	App\\Models\\User	1	auth_token	682e02aef25f8f8bc2b7495be9caab1aa0d4db68cdb886b458cf678ffbd82fe0	["*"]	\N	\N	2026-07-01 06:46:14	2026-07-01 06:46:14
13	App\\Models\\User	1	auth_token	1a5197319a74851d923a745152230b3a5471fab85267d1e60667e2d333c5176a	["*"]	\N	\N	2026-07-01 06:46:14	2026-07-01 06:46:14
14	App\\Models\\User	1	auth_token	3ac56116803b215a569bff5ae713208029d8906f77bfe26ccab40adcc712663f	["*"]	\N	\N	2026-07-01 06:46:14	2026-07-01 06:46:14
15	App\\Models\\User	1	auth_token	7b3fd3e0571061663a63afc263b8f99a2ee6f5767461170775f0635c1f35e43e	["*"]	\N	\N	2026-07-01 06:46:15	2026-07-01 06:46:15
18	App\\Models\\User	1	auth_token	4fd5b43e07fb686c321a3ac82b287df7dc63a0288efbca92b455c06c6af67506	["*"]	\N	\N	2026-07-01 07:37:28	2026-07-01 07:37:28
17	App\\Models\\User	1	auth_token	cc29e9223d842aec95b02920a7c628e0b6992d76f051055c02ce93c506aa30a4	["*"]	\N	\N	2026-07-01 07:37:28	2026-07-01 07:37:28
16	App\\Models\\User	1	auth_token	5c3825fa9acf10cfa8cbf04bddef8af6a67b672a2d75a99bfc3bbbede2aeebac	["*"]	\N	\N	2026-07-01 07:37:28	2026-07-01 07:37:28
19	App\\Models\\User	1	auth_token	a8e1c079e38b6127655ffda8e6ade9c337f7426cbf1273859f21ca0814180a18	["*"]	\N	\N	2026-07-01 07:37:28	2026-07-01 07:37:28
20	App\\Models\\User	1	auth_token	598bc0303c4480d3f6933d80995757a691eaee9b941248e4bbd52d80a18a3310	["*"]	\N	\N	2026-07-01 07:37:32	2026-07-01 07:37:32
21	App\\Models\\User	1	auth_token	ac1d3686f2dbcf9a43608dec3328ecf421d9a1f6c3823b0ab6621e0e53d71579	["*"]	\N	\N	2026-07-01 07:37:32	2026-07-01 07:37:32
22	App\\Models\\User	1	auth_token	0e4a76b3079e9b45908e7b49580cdb857fd6c01c16880a5cb74125a07cb158d1	["*"]	\N	\N	2026-07-01 07:37:32	2026-07-01 07:37:32
23	App\\Models\\User	1	auth_token	c770ad7a6f431950ea8ee026507c4644fea1e8bb57ef8090bf3d8e946ba9b974	["*"]	\N	\N	2026-07-01 07:51:58	2026-07-01 07:51:58
24	App\\Models\\User	1	auth_token	70e517c629192b84952172efada00af86a9b2c010e81f131c060b2c122b68886	["*"]	\N	\N	2026-07-01 07:51:58	2026-07-01 07:51:58
25	App\\Models\\User	1	auth_token	708d2fed593153bb3e36288351b4dc7323c7fa74a10e9326342b32fe4a43a775	["*"]	\N	\N	2026-07-01 07:52:11	2026-07-01 07:52:11
26	App\\Models\\User	1	auth_token	9e2c843c964a8b0144782dbc8c2fd039e6dcc81013ca89de634e7cb39efe7c8a	["*"]	\N	\N	2026-07-01 07:52:11	2026-07-01 07:52:11
27	App\\Models\\User	1	auth_token	dca91fd3b16dd2c2c33a0c5fc72ce8ca1b10fabf35488b272a1230eadb4117bc	["*"]	\N	\N	2026-07-01 07:52:11	2026-07-01 07:52:11
28	App\\Models\\User	1	auth_token	8e36f1b39563d32072227c83271c0f9d37ee457c30d8634457d5c848236dce44	["*"]	\N	\N	2026-07-01 07:52:11	2026-07-01 07:52:11
29	App\\Models\\User	1	auth_token	1e8ff0a79484484e5a35d5e4f6809ac4fb19e08e53af0095dd7c96e138ec2397	["*"]	\N	\N	2026-07-01 07:52:13	2026-07-01 07:52:13
39	App\\Models\\User	1	auth_token	153a88ba8c83565c7233a0c4a8e0ccc6ca2ef7f3dab4d58fc2d77537264c818f	["*"]	\N	\N	2026-07-13 19:00:54	2026-07-13 19:00:54
40	App\\Models\\User	1	auth_token	5d1316cf48f73b62d02dd0a7f2b35234d42e3bb9083ea099fe15c0c90c95f727	["*"]	\N	\N	2026-07-13 19:00:54	2026-07-13 19:00:54
30	App\\Models\\User	1	auth_token	3c173f1442c5d1526d2b76316a018565499eb1933d8cb0e0ab40ef0085761fdf	["*"]	2026-07-06 05:39:21	\N	2026-07-01 07:52:13	2026-07-06 05:39:21
31	App\\Models\\User	1	auth_token	6a014c60953d1e55b3e8e080a1c2d71465391f9b36f84302061fe3047e3c2b36	["*"]	\N	\N	2026-07-06 06:36:33	2026-07-06 06:36:33
33	App\\Models\\User	1	auth_token	a64c26c7c7be7a9a4efb1fb833ef006a7ada9c520ce2bf0711851d9c7985f3d9	["*"]	\N	\N	2026-07-06 06:36:36	2026-07-06 06:36:36
34	App\\Models\\User	1	auth_token	a481aefee18ab28a21ab7fb2c7ea1670c4ab07248f4078a8fcaa2e41e1be51e5	["*"]	\N	\N	2026-07-06 06:36:36	2026-07-06 06:36:36
35	App\\Models\\User	1	auth_token	cd80a8e383243a39c54bb5b1bd9585cf0d795ade8bafb5fa018cf82050bd3a7c	["*"]	\N	\N	2026-07-06 06:36:36	2026-07-06 06:36:36
36	App\\Models\\User	1	auth_token	d2bc7f15050e735a913cdcbe605629274c7785a4fe44c04f2d52e2ffe3b98101	["*"]	\N	\N	2026-07-06 06:36:40	2026-07-06 06:36:40
41	App\\Models\\User	1	auth_token	a818fcc9cd545c5d4a85f292670de423de730485264fcabec46fabedef7b70c4	["*"]	\N	\N	2026-07-13 19:01:07	2026-07-13 19:01:07
42	App\\Models\\User	1	auth_token	1186b6225fa3a3672eefbdb68e08520209f042a3a60fdd566c299f8ae35b092c	["*"]	\N	\N	2026-07-13 19:35:22	2026-07-13 19:35:22
49	App\\Models\\User	1	auth_token	dd778794ab36a1e413a401f70b45525b83c6328feb73dd065052aaeac4032852	["*"]	\N	\N	2026-07-15 03:48:04	2026-07-15 03:48:04
43	App\\Models\\User	1	auth_token	bfedfbf184fa5264f2a122b97d0c27c57a3c46c504618acc4ff05f2660e390a8	["*"]	\N	\N	2026-07-13 19:35:23	2026-07-13 19:35:23
44	App\\Models\\User	1	auth_token	fc4de660fed17bc735af239bffc481da3d86d4896d72cf567cf6ce58ae799ecc	["*"]	\N	\N	2026-07-13 19:38:13	2026-07-13 19:38:13
46	App\\Models\\User	1	auth_token	dc7b9b277ef859f85e2e28960fe4d3e49ccc3faa2be2eccf91d4b83943fc0fa4	["*"]	\N	\N	2026-07-13 19:38:13	2026-07-13 19:38:13
71	App\\Models\\User	2	auth_token	936fd0713c656009327f1d43985e37f37495614bc737cf48673ddcd24c866961	["*"]	2026-07-16 03:31:14	\N	2026-07-16 03:16:39	2026-07-16 03:31:14
50	App\\Models\\User	2	auth_token	68051afa8b0dcb7b8307a24a2f83523b2f0c793a567fe5bf72986040e6f0089f	["*"]	\N	\N	2026-07-15 03:53:28	2026-07-15 03:53:28
63	App\\Models\\User	1	auth_token	de69f24cf5bcfbaa22e3949ae8d8d0f01fabec7a0e78a4f05577784d5ec98200	["*"]	2026-07-15 21:58:04	\N	2026-07-15 21:42:24	2026-07-15 21:58:04
52	App\\Models\\User	2	auth_token	0806af820aea25775ae69b0b3237859bfbf2a4eda4ef03b9b15914071c7eb901	["*"]	\N	\N	2026-07-15 03:53:38	2026-07-15 03:53:38
53	App\\Models\\User	1	auth_token	ffa56f94b98415902b82865da04587c985429dac0ab4b61eadf22f093f1c1d61	["*"]	\N	\N	2026-07-15 05:13:27	2026-07-15 05:13:27
54	App\\Models\\User	3	auth_token	e7ceabb02b504fea652f3c30d5b2a0c8c45685a1a96ca930d250007754beda92	["*"]	\N	\N	2026-07-15 05:14:33	2026-07-15 05:14:33
69	App\\Models\\User	1	auth_token	ab991dde0bf9e49bd0013624b2488c0c7f2dbd32ed2a9b5913f00f08dd126a90	["*"]	2026-07-16 03:03:38	\N	2026-07-16 03:03:29	2026-07-16 03:03:38
59	App\\Models\\User	1	auth_token	03442c9fae3691ec8583d0f3cf51a2be619530e8dc31442186243583f3e0b857	["*"]	2026-07-15 06:57:09	\N	2026-07-15 06:48:53	2026-07-15 06:57:09
64	App\\Models\\User	1	auth_token	83336aa618168075653061bd3beb51212ff9c2cb37df8a5be41eddaffd906417	["*"]	2026-07-16 00:46:29	\N	2026-07-16 00:30:09	2026-07-16 00:46:29
67	App\\Models\\User	1	auth_token	2c230ce7f7a72e4d6a59f20f19ab096a40d2e15324ff65e3072e330e8d5e4140	["*"]	\N	\N	2026-07-16 01:23:57	2026-07-16 01:23:57
68	App\\Models\\User	1	auth_token	3d971641463c01c16e449b66081554b597e096317adb3dd25172a5357844a9a8	["*"]	\N	\N	2026-07-16 02:47:36	2026-07-16 02:47:36
70	App\\Models\\User	1	auth_token	8f960d03a098a7e1a43dd7a27380ac838f0906947b870bab9bcc52908b2cee88	["*"]	2026-07-16 03:07:55	\N	2026-07-16 03:07:39	2026-07-16 03:07:55
60	App\\Models\\User	1	auth_token	1d6357cff4c3f0e3b0a7bf04f2958823a8e8f5a61e2d8ab9adbdd178c3032ca2	["*"]	2026-07-15 21:04:00	\N	2026-07-15 20:18:05	2026-07-15 21:04:00
58	App\\Models\\User	3	auth_token	05e9cc51aeca681086a3b4df20239041aa512705caaebc93a22ab1583e6af5de	["*"]	\N	\N	2026-07-15 06:07:52	2026-07-15 06:07:52
65	App\\Models\\User	1	auth_token	b8a71d15fad72c43dcda3641f812d02bd201925f25711af229a9a38cfd62dce1	["*"]	2026-07-16 00:50:13	\N	2026-07-16 00:47:03	2026-07-16 00:50:13
66	App\\Models\\User	1	auth_token	8d8922c55ae4252dfd960dc9659f9842a55228274342dccf185b5bfe25ce85f0	["*"]	\N	\N	2026-07-16 00:52:35	2026-07-16 00:52:35
61	App\\Models\\User	1	auth_token	8359df8a686d989057813d6f9ab1dbb8693af6496bf4a2e803236163bf4a4b97	["*"]	2026-07-15 21:15:34	\N	2026-07-15 21:04:04	2026-07-15 21:15:34
62	App\\Models\\User	1	auth_token	8aa87074619fe183a5a16f48fcecd97cd754cebb0e16cd67b85b2614202417a3	["*"]	\N	\N	2026-07-15 21:31:24	2026-07-15 21:31:24
73	App\\Models\\User	1	auth_token	39aeafd888f3ed46ec248868fb4387ebff3882d7927928d9886ac9d0234b25fb	["*"]	2026-07-17 04:59:52	\N	2026-07-17 04:55:43	2026-07-17 04:59:52
\.


--
-- Data for Name: provincias; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.provincias (id, pais_id, nombre, created_at, updated_at) FROM stdin;
1	1	Santa Elena	2026-07-16 02:41:55	2026-07-16 02:41:55
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.roles (id, nombre, descripcion, created_at, updated_at) FROM stdin;
1	Administrador	Acceso total al sistema	2026-07-16 02:41:54	2026-07-16 02:41:54
2	Responsable	Gestiona y atiende incidencias asignadas	2026-07-16 02:41:54	2026-07-16 02:41:54
3	Ciudadano	Registra y consulta incidencias	2026-07-16 02:41:54	2026-07-16 02:41:54
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
kGbEcoW5Z79cSNKTlFnUwwX4KeSfekrEPUmmo968	\N	172.18.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJVeGoyOWFLZ1Fmc244T3lhbWx0TFREQ0w1dkdvSkRYNTlMeVlXWkJRIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDgwXC9pbmNpZGVuY2lhcyIsInJvdXRlIjoiaW5jaWRlbmNpYXMuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==	1784264208
\.


--
-- Data for Name: subtipos_incidencia; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.subtipos_incidencia (id, tipo_incidencia_id, nombre, descripcion, created_at, updated_at) FROM stdin;
1	1	Alumbrado público	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
2	1	Bache en vía	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
3	1	Semáforo dañado	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
4	1	Alcantarillado	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
5	1	Vía en mal estado	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
6	2	Robo	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
7	2	Vandalismo	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
8	2	Riña / Altercado	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
9	2	Punto de venta de drogas	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
10	3	Acumulación de basura	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
11	3	Contaminación de agua	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
12	3	Tala ilegal	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
13	3	Quema no autorizada	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
14	4	Corte de agua potable	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
15	4	Corte de energía eléctrica	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
16	4	Fuga de agua	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
17	5	Foco de plagas	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
18	5	Animal en la vía pública	\N	2026-07-01 05:44:25	2026-07-01 05:44:25
\.


--
-- Data for Name: tipos_incidencia; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.tipos_incidencia (id, nombre, descripcion, created_at, updated_at) FROM stdin;
1	Infraestructura	Incidencias relacionadas con Infraestructura	2026-07-01 05:44:24	2026-07-01 05:44:24
2	Seguridad	Incidencias relacionadas con Seguridad	2026-07-01 05:44:25	2026-07-01 05:44:25
3	Ambiental	Incidencias relacionadas con Ambiental	2026-07-01 05:44:25	2026-07-01 05:44:25
4	Servicios Básicos	Incidencias relacionadas con Servicios Básicos	2026-07-01 05:44:25	2026-07-01 05:44:25
5	Salud Pública	Incidencias relacionadas con Salud Pública	2026-07-01 05:44:25	2026-07-01 05:44:25
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: incidencias_user
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, rol_id, apellido, activo) FROM stdin;
2	Skay	skayalvaradorodriguez@gmail.com	\N	$2y$12$Hx1quMmYEjPNT/FmZvZw0eCTw1YX6E1CKrVozfa8mt7uwAyc8B7VO	\N	2026-07-11 02:08:33	2026-07-15 03:52:43	2	Alvarado	t
3	Gisell	gisell@gmail.com	\N	$2y$12$bq4HMsHD.aWYv26oXpSI.uZPU319cAHuKfP2xDSMMHvpSdmOdRAx2	\N	2026-07-15 05:14:31	2026-07-15 05:14:31	3	Rodriguez	t
4	Kerlly	kerlly@gmail.com	\N	$2y$12$DXXqgGJNoQU/WSdsovHOi..zudBFfZEA5oxwVr2FmeDbEy.StkGEO	\N	2026-07-15 06:00:18	2026-07-15 06:00:18	3	Mite	t
1	Administrador	admin@incidencias.com	\N	$2y$12$OL8wuClzXiUCkpOBE5t6c.pzjwjBLSco0ytFeITdyA8JUNMbnlH7i	\N	2026-07-16 02:41:56	2026-07-16 02:41:56	1	Sistema	t
5	Carlos	responsable@incidencias.com	\N	$2y$12$ikK9.338w4OYagkkw0o0eu7TxwaOJKYzZlWFp/f7cv6jTEFu/dIHm	\N	2026-07-16 02:41:57	2026-07-16 02:41:57	2	Mendoza	t
6	Maria	ciudadano@incidencias.com	\N	$2y$12$lJX1R3u.ceWHzyhsTc1VZuI66we3V6gz1Qf2BxBug0kAB/e8kUMwS	\N	2026-07-16 02:41:57	2026-07-16 02:41:57	3	Lopez	t
\.


--
-- Name: asignaciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.asignaciones_id_seq', 3, true);


--
-- Name: ciudades_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.ciudades_id_seq', 3, true);


--
-- Name: comentarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.comentarios_id_seq', 4, true);


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

SELECT pg_catalog.setval('public.historial_estados_id_seq', 45, true);


--
-- Name: incidencias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.incidencias_id_seq', 39, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.migrations_id_seq', 21, true);


--
-- Name: notificaciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.notificaciones_id_seq', 10, true);


--
-- Name: paises_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.paises_id_seq', 1, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 73, true);


--
-- Name: provincias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.provincias_id_seq', 1, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: incidencias_user
--

SELECT pg_catalog.setval('public.roles_id_seq', 3, true);


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

SELECT pg_catalog.setval('public.users_id_seq', 6, true);


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

\unrestrict d1lCH9sUldQ3FF1iKnYAqnfbeQmrll8WJm8mNs3YQ3GT8deHrf2JHwKpm0QOq93

