SELECT setval('curso_curso_id_seq', coalesce(max(curso_id),0) + 1, false) FROM curso;
SELECT setval('curso_disciplina_id_seq', coalesce(max(id),0) + 1, false) FROM curso_disciplina;
SELECT setval('curso_horario_id_seq', coalesce(max(id),0) + 1, false) FROM curso_horario;
SELECT setval('disciplina_disciplina_id_seq', coalesce(max(disciplina_id),0) + 1, false) FROM disciplina;
SELECT setval('horarios_horarios_id_seq', coalesce(max(horarios_id),0) + 1, false) FROM horarios;
SELECT setval('salas_sala_id_seq', coalesce(max(sala_id),0) + 1, false) FROM salas;
SELECT setval('turma_turma_id_seq', coalesce(max(turma_id),0) + 1, false) FROM turma;