begin; 

CREATE TABLE curso( 
      curso_id number(10)    NOT NULL , 
      nome_curso CLOB    NOT NULL , 
      tipo_curso CLOB    NOT NULL , 
      modalidade CLOB    NOT NULL , 
      turno_curso CLOB    NOT NULL , 
 PRIMARY KEY (curso_id)); 

 CREATE TABLE curso_disciplina( 
      id number(10)    NOT NULL , 
      curso_id number(10)    NOT NULL , 
      disciplina_id number(10)    NOT NULL , 
 PRIMARY KEY (id)); 

 CREATE TABLE curso_horario( 
      id number(10)    NOT NULL , 
      horarios_id number(10)    NOT NULL , 
      curso_id number(10)    NOT NULL , 
 PRIMARY KEY (id)); 

 CREATE TABLE disciplina( 
      disciplina_id number(10)    NOT NULL , 
      nome_disc CLOB    NOT NULL , 
      carga_h number(10)  (3)    NOT NULL , 
 PRIMARY KEY (disciplina_id)); 

 CREATE TABLE horarios( 
      horarios_id number(10)    NOT NULL , 
      horario time    NOT NULL , 
      dia_sem CLOB   , 
 PRIMARY KEY (horarios_id)); 

 CREATE TABLE salas( 
      sala_id number(10)    NOT NULL , 
      numero_sala number(10)    NOT NULL , 
      capacidade_sala number(10)  (2)    NOT NULL , 
      tipo_sala CLOB   , 
 PRIMARY KEY (sala_id)); 

 CREATE TABLE turma( 
      turma_id number(10)    NOT NULL , 
      tipo_turma CLOB    NOT NULL , 
      n_alunos number(10)  (2)    NOT NULL , 
      curso_id number(10)    NOT NULL , 
 PRIMARY KEY (turma_id)); 

  
 ALTER TABLE salas ADD UNIQUE (numero_sala);
  
 ALTER TABLE curso_disciplina ADD CONSTRAINT fk_curso_disciplina_2 FOREIGN KEY (disciplina_id) references disciplina(disciplina_id); 
ALTER TABLE curso_disciplina ADD CONSTRAINT fk_curso_disciplina_1 FOREIGN KEY (curso_id) references curso(curso_id); 
ALTER TABLE curso_horario ADD CONSTRAINT fk_curso_horarios_2 FOREIGN KEY (curso_id) references curso(curso_id); 
ALTER TABLE curso_horario ADD CONSTRAINT fk_curso_horarios_1 FOREIGN KEY (horarios_id) references horarios(horarios_id); 
ALTER TABLE turma ADD CONSTRAINT fk_turma_1 FOREIGN KEY (curso_id) references curso(curso_id); 
 CREATE SEQUENCE curso_curso_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER curso_curso_id_seq_tr 

BEFORE INSERT ON curso FOR EACH ROW 

WHEN 

(NEW.curso_id IS NULL) 

BEGIN 

SELECT curso_curso_id_seq.NEXTVAL INTO :NEW.curso_id FROM DUAL; 

END;
CREATE SEQUENCE curso_disciplina_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER curso_disciplina_id_seq_tr 

BEFORE INSERT ON curso_disciplina FOR EACH ROW 

WHEN 

(NEW.id IS NULL) 

BEGIN 

SELECT curso_disciplina_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE curso_horario_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER curso_horario_id_seq_tr 

BEFORE INSERT ON curso_horario FOR EACH ROW 

WHEN 

(NEW.id IS NULL) 

BEGIN 

SELECT curso_horario_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE disciplina_disciplina_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER disciplina_disciplina_id_seq_tr 

BEFORE INSERT ON disciplina FOR EACH ROW 

WHEN 

(NEW.disciplina_id IS NULL) 

BEGIN 

SELECT disciplina_disciplina_id_seq.NEXTVAL INTO :NEW.disciplina_id FROM DUAL; 

END;
CREATE SEQUENCE horarios_horarios_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER horarios_horarios_id_seq_tr 

BEFORE INSERT ON horarios FOR EACH ROW 

WHEN 

(NEW.horarios_id IS NULL) 

BEGIN 

SELECT horarios_horarios_id_seq.NEXTVAL INTO :NEW.horarios_id FROM DUAL; 

END;
CREATE SEQUENCE salas_sala_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER salas_sala_id_seq_tr 

BEFORE INSERT ON salas FOR EACH ROW 

WHEN 

(NEW.sala_id IS NULL) 

BEGIN 

SELECT salas_sala_id_seq.NEXTVAL INTO :NEW.sala_id FROM DUAL; 

END;
CREATE SEQUENCE turma_turma_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER turma_turma_id_seq_tr 

BEFORE INSERT ON turma FOR EACH ROW 

WHEN 

(NEW.turma_id IS NULL) 

BEGIN 

SELECT turma_turma_id_seq.NEXTVAL INTO :NEW.turma_id FROM DUAL; 

END;
 
  
 
 commit;