PGDMP  .    ,    
    
        |         
   softexpert    16.2    16.2 ,    �           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                      false            �           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                      false            �           0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                      false            �           1262    16596 
   softexpert    DATABASE     �   CREATE DATABASE softexpert WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'Portuguese_Brazil.utf8';
    DROP DATABASE softexpert;
                postgres    false                        2615    16597    desafio    SCHEMA        CREATE SCHEMA desafio;
    DROP SCHEMA desafio;
                postgres    false            �            1259    16599    imposto    TABLE     w   CREATE TABLE desafio.imposto (
    id_imposto integer NOT NULL,
    valor_percentual_imposto numeric(18,2) NOT NULL
);
    DROP TABLE desafio.imposto;
       desafio         heap    postgres    false    6            �            1259    16598    imposto_id_imposto_seq    SEQUENCE     �   CREATE SEQUENCE desafio.imposto_id_imposto_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 .   DROP SEQUENCE desafio.imposto_id_imposto_seq;
       desafio          postgres    false    6    217            �           0    0    imposto_id_imposto_seq    SEQUENCE OWNED BY     S   ALTER SEQUENCE desafio.imposto_id_imposto_seq OWNED BY desafio.imposto.id_imposto;
          desafio          postgres    false    216            �            1259    16618    produto    TABLE     �   CREATE TABLE desafio.produto (
    id_produto integer NOT NULL,
    id_tipo integer,
    nome_produto character varying(100) NOT NULL,
    valor_venda_produto numeric(18,2) NOT NULL
);
    DROP TABLE desafio.produto;
       desafio         heap    postgres    false    6            �            1259    16617    produto_id_produto_seq    SEQUENCE     �   CREATE SEQUENCE desafio.produto_id_produto_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 .   DROP SEQUENCE desafio.produto_id_produto_seq;
       desafio          postgres    false    6    221            �           0    0    produto_id_produto_seq    SEQUENCE OWNED BY     S   ALTER SEQUENCE desafio.produto_id_produto_seq OWNED BY desafio.produto.id_produto;
          desafio          postgres    false    220            �            1259    16606    tipo    TABLE     �   CREATE TABLE desafio.tipo (
    id_tipo integer NOT NULL,
    id_imposto integer,
    nome_tipo character varying(100) NOT NULL
);
    DROP TABLE desafio.tipo;
       desafio         heap    postgres    false    6            �            1259    16605    tipo_id_tipo_seq    SEQUENCE     �   CREATE SEQUENCE desafio.tipo_id_tipo_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 (   DROP SEQUENCE desafio.tipo_id_tipo_seq;
       desafio          postgres    false    219    6            �           0    0    tipo_id_tipo_seq    SEQUENCE OWNED BY     G   ALTER SEQUENCE desafio.tipo_id_tipo_seq OWNED BY desafio.tipo.id_tipo;
          desafio          postgres    false    218            �            1259    16630    venda    TABLE     �   CREATE TABLE desafio.venda (
    id_venda integer NOT NULL,
    quantidade_total_venda integer NOT NULL,
    valor_total_venda numeric(18,2) NOT NULL,
    valor_total_imposto_venda numeric(18,2) NOT NULL,
    data_venda timestamp without time zone
);
    DROP TABLE desafio.venda;
       desafio         heap    postgres    false    6            �            1259    16629    venda_id_venda_seq    SEQUENCE     �   CREATE SEQUENCE desafio.venda_id_venda_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 *   DROP SEQUENCE desafio.venda_id_venda_seq;
       desafio          postgres    false    6    223            �           0    0    venda_id_venda_seq    SEQUENCE OWNED BY     K   ALTER SEQUENCE desafio.venda_id_venda_seq OWNED BY desafio.venda.id_venda;
          desafio          postgres    false    222            �            1259    16658    venda_produto    TABLE     �  CREATE TABLE desafio.venda_produto (
    id_venda_produto integer NOT NULL,
    id_venda integer,
    id_produto integer,
    quantidade_venda_produto integer NOT NULL,
    valor_produto_venda_produto numeric(18,2) NOT NULL,
    valor_total_produto_venda_produto numeric(18,2) NOT NULL,
    valor_imposto_venda_produto numeric(18,2) NOT NULL,
    valor_total_imposto_venda_produto numeric(18,2) NOT NULL
);
 "   DROP TABLE desafio.venda_produto;
       desafio         heap    postgres    false    6            �            1259    16657 "   venda_produto_id_venda_produto_seq    SEQUENCE     �   CREATE SEQUENCE desafio.venda_produto_id_venda_produto_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 :   DROP SEQUENCE desafio.venda_produto_id_venda_produto_seq;
       desafio          postgres    false    6    225            �           0    0 "   venda_produto_id_venda_produto_seq    SEQUENCE OWNED BY     k   ALTER SEQUENCE desafio.venda_produto_id_venda_produto_seq OWNED BY desafio.venda_produto.id_venda_produto;
          desafio          postgres    false    224            /           2604    16602    imposto id_imposto    DEFAULT     z   ALTER TABLE ONLY desafio.imposto ALTER COLUMN id_imposto SET DEFAULT nextval('desafio.imposto_id_imposto_seq'::regclass);
 B   ALTER TABLE desafio.imposto ALTER COLUMN id_imposto DROP DEFAULT;
       desafio          postgres    false    217    216    217            1           2604    16621    produto id_produto    DEFAULT     z   ALTER TABLE ONLY desafio.produto ALTER COLUMN id_produto SET DEFAULT nextval('desafio.produto_id_produto_seq'::regclass);
 B   ALTER TABLE desafio.produto ALTER COLUMN id_produto DROP DEFAULT;
       desafio          postgres    false    220    221    221            0           2604    16609    tipo id_tipo    DEFAULT     n   ALTER TABLE ONLY desafio.tipo ALTER COLUMN id_tipo SET DEFAULT nextval('desafio.tipo_id_tipo_seq'::regclass);
 <   ALTER TABLE desafio.tipo ALTER COLUMN id_tipo DROP DEFAULT;
       desafio          postgres    false    219    218    219            2           2604    16633    venda id_venda    DEFAULT     r   ALTER TABLE ONLY desafio.venda ALTER COLUMN id_venda SET DEFAULT nextval('desafio.venda_id_venda_seq'::regclass);
 >   ALTER TABLE desafio.venda ALTER COLUMN id_venda DROP DEFAULT;
       desafio          postgres    false    223    222    223            3           2604    16661    venda_produto id_venda_produto    DEFAULT     �   ALTER TABLE ONLY desafio.venda_produto ALTER COLUMN id_venda_produto SET DEFAULT nextval('desafio.venda_produto_id_venda_produto_seq'::regclass);
 N   ALTER TABLE desafio.venda_produto ALTER COLUMN id_venda_produto DROP DEFAULT;
       desafio          postgres    false    224    225    225            �          0    16599    imposto 
   TABLE DATA           H   COPY desafio.imposto (id_imposto, valor_percentual_imposto) FROM stdin;
    desafio          postgres    false    217   ,4       �          0    16618    produto 
   TABLE DATA           Z   COPY desafio.produto (id_produto, id_tipo, nome_produto, valor_venda_produto) FROM stdin;
    desafio          postgres    false    221   Q4       �          0    16606    tipo 
   TABLE DATA           ?   COPY desafio.tipo (id_tipo, id_imposto, nome_tipo) FROM stdin;
    desafio          postgres    false    219   �4       �          0    16630    venda 
   TABLE DATA           |   COPY desafio.venda (id_venda, quantidade_total_venda, valor_total_venda, valor_total_imposto_venda, data_venda) FROM stdin;
    desafio          postgres    false    223   �4       �          0    16658    venda_produto 
   TABLE DATA           �   COPY desafio.venda_produto (id_venda_produto, id_venda, id_produto, quantidade_venda_produto, valor_produto_venda_produto, valor_total_produto_venda_produto, valor_imposto_venda_produto, valor_total_imposto_venda_produto) FROM stdin;
    desafio          postgres    false    225   �4       �           0    0    imposto_id_imposto_seq    SEQUENCE SET     E   SELECT pg_catalog.setval('desafio.imposto_id_imposto_seq', 1, true);
          desafio          postgres    false    216            �           0    0    produto_id_produto_seq    SEQUENCE SET     E   SELECT pg_catalog.setval('desafio.produto_id_produto_seq', 1, true);
          desafio          postgres    false    220            �           0    0    tipo_id_tipo_seq    SEQUENCE SET     ?   SELECT pg_catalog.setval('desafio.tipo_id_tipo_seq', 1, true);
          desafio          postgres    false    218            �           0    0    venda_id_venda_seq    SEQUENCE SET     A   SELECT pg_catalog.setval('desafio.venda_id_venda_seq', 1, true);
          desafio          postgres    false    222            �           0    0 "   venda_produto_id_venda_produto_seq    SEQUENCE SET     Q   SELECT pg_catalog.setval('desafio.venda_produto_id_venda_produto_seq', 1, true);
          desafio          postgres    false    224            5           2606    16604    imposto imposto_pkey 
   CONSTRAINT     [   ALTER TABLE ONLY desafio.imposto
    ADD CONSTRAINT imposto_pkey PRIMARY KEY (id_imposto);
 ?   ALTER TABLE ONLY desafio.imposto DROP CONSTRAINT imposto_pkey;
       desafio            postgres    false    217            9           2606    16623    produto produto_pkey 
   CONSTRAINT     [   ALTER TABLE ONLY desafio.produto
    ADD CONSTRAINT produto_pkey PRIMARY KEY (id_produto);
 ?   ALTER TABLE ONLY desafio.produto DROP CONSTRAINT produto_pkey;
       desafio            postgres    false    221            7           2606    16611    tipo tipo_pkey 
   CONSTRAINT     R   ALTER TABLE ONLY desafio.tipo
    ADD CONSTRAINT tipo_pkey PRIMARY KEY (id_tipo);
 9   ALTER TABLE ONLY desafio.tipo DROP CONSTRAINT tipo_pkey;
       desafio            postgres    false    219            ;           2606    16635    venda venda_pkey 
   CONSTRAINT     U   ALTER TABLE ONLY desafio.venda
    ADD CONSTRAINT venda_pkey PRIMARY KEY (id_venda);
 ;   ALTER TABLE ONLY desafio.venda DROP CONSTRAINT venda_pkey;
       desafio            postgres    false    223            =           2606    16663     venda_produto venda_produto_pkey 
   CONSTRAINT     m   ALTER TABLE ONLY desafio.venda_produto
    ADD CONSTRAINT venda_produto_pkey PRIMARY KEY (id_venda_produto);
 K   ALTER TABLE ONLY desafio.venda_produto DROP CONSTRAINT venda_produto_pkey;
       desafio            postgres    false    225            ?           2606    16624    produto produto_id_tipo_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY desafio.produto
    ADD CONSTRAINT produto_id_tipo_fkey FOREIGN KEY (id_tipo) REFERENCES desafio.tipo(id_tipo) ON DELETE CASCADE;
 G   ALTER TABLE ONLY desafio.produto DROP CONSTRAINT produto_id_tipo_fkey;
       desafio          postgres    false    219    4663    221            >           2606    16612    tipo tipo_id_imposto_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY desafio.tipo
    ADD CONSTRAINT tipo_id_imposto_fkey FOREIGN KEY (id_imposto) REFERENCES desafio.imposto(id_imposto) ON DELETE CASCADE;
 D   ALTER TABLE ONLY desafio.tipo DROP CONSTRAINT tipo_id_imposto_fkey;
       desafio          postgres    false    4661    217    219            @           2606    16669 +   venda_produto venda_produto_id_produto_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY desafio.venda_produto
    ADD CONSTRAINT venda_produto_id_produto_fkey FOREIGN KEY (id_produto) REFERENCES desafio.produto(id_produto) ON DELETE CASCADE;
 V   ALTER TABLE ONLY desafio.venda_produto DROP CONSTRAINT venda_produto_id_produto_fkey;
       desafio          postgres    false    221    4665    225            A           2606    16664 )   venda_produto venda_produto_id_venda_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY desafio.venda_produto
    ADD CONSTRAINT venda_produto_id_venda_fkey FOREIGN KEY (id_venda) REFERENCES desafio.venda(id_venda) ON DELETE CASCADE;
 T   ALTER TABLE ONLY desafio.venda_produto DROP CONSTRAINT venda_produto_id_venda_fkey;
       desafio          postgres    false    4667    223    225            �      x�3�4�г������ 2�      �       x�3�4�t�ONTp��I�Գ������ E�      �      x�3�4�JM+�LO-J�+I����� H�      �   .   x�3�4�4��33�4�32�LtLt��L-�L��b���� ���      �   )   x�3�4BcNK=KN#K=3sNC=KKNS=3c�=... a"k     