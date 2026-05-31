#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script to generate Draw.io XML for ERD Logical
Format: Entity name (header), Keys (left column), Name (right column)
No data types
"""

import xml.etree.ElementTree as ET
from xml.dom import minidom
import html

# Entities data - only Name and Keys (no data types)
entities_data = {
    'users': [
        ('id', 'PK'),
        ('name', ''),
        ('email', 'UK'),
        ('email_verified_at', ''),
        ('password', ''),
        ('bookmarks', ''),
        ('role', ''),
        ('remember_token', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'topics': [
        ('id', 'PK'),
        ('user_id', 'FK'),
        ('title', ''),
        ('slug', 'UK'),
        ('content', ''),
        ('status', ''),
        ('approved_by', 'FK'),
        ('category_id', 'FK'),
        ('tags', ''),
        ('is_locked', ''),
        ('is_edited', ''),
        ('edited_by', 'FK'),
        ('view_count', ''),
        ('created_at', ''),
        ('updated_at', ''),
        ('deleted_at', ''),
    ],
    'categories': [
        ('id', 'PK'),
        ('name', ''),
        ('slug', 'UK'),
        ('description', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'category_topic': [
        ('id', 'PK'),
        ('category_id', 'FK'),
        ('topic_id', 'FK'),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'answers': [
        ('id', 'PK'),
        ('topic_id', 'FK'),
        ('user_id', 'FK'),
        ('content', ''),
        ('images', ''),
        ('is_verified', ''),
        ('verified_by', 'FK'),
        ('vote_count', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'answer_comments': [
        ('id', 'PK'),
        ('answer_id', 'FK'),
        ('user_id', 'FK'),
        ('content', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'answer_votes': [
        ('id', 'PK'),
        ('answer_id', 'FK'),
        ('user_id', 'FK'),
        ('vote', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'topic_attachments': [
        ('id', 'PK'),
        ('topic_id', 'FK'),
        ('uploaded_by', 'FK'),
        ('file_path', ''),
        ('file_type', ''),
        ('file_size', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'topic_votes': [
        ('id', 'PK'),
        ('topic_id', 'FK'),
        ('user_id', 'FK'),
        ('vote', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'lessons': [
        ('id', 'PK'),
        ('title', ''),
        ('description', ''),
        ('category_id', 'FK'),
        ('file_path', ''),
        ('file_name', ''),
        ('file_type', ''),
        ('created_by', 'FK'),
        ('is_published', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'quizzes': [
        ('id', 'PK'),
        ('lesson_id', 'FK'),
        ('title', ''),
        ('description', ''),
        ('time_limit', ''),
        ('passing_score', ''),
        ('is_published', ''),
        ('allow_retry', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'quiz_questions': [
        ('id', 'PK'),
        ('quiz_id', 'FK'),
        ('question', ''),
        ('option_a', ''),
        ('option_b', ''),
        ('option_c', ''),
        ('option_d', ''),
        ('correct_answer', ''),
        ('points', ''),
        ('order', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'user_quiz_attempts': [
        ('id', 'PK'),
        ('user_id', 'FK'),
        ('quiz_id', 'FK'),
        ('score', ''),
        ('total_questions', ''),
        ('correct_answers', ''),
        ('answers', ''),
        ('started_at', ''),
        ('completed_at', ''),
        ('is_passed', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'user_lesson_progress': [
        ('id', 'PK'),
        ('user_id', 'FK'),
        ('lesson_id', 'FK'),
        ('progress', ''),
        ('scroll_position', ''),
        ('time_spent', ''),
        ('is_completed', ''),
        ('started_at', ''),
        ('completed_at', ''),
        ('last_accessed_at', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'document_templates': [
        ('id', 'PK'),
        ('name', ''),
        ('slug', 'UK'),
        ('description', ''),
        ('latest_version_id', ''),
        ('latest_version_number', ''),
        ('created_by', 'FK'),
        ('updated_by', 'FK'),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'document_template_versions': [
        ('id', 'PK'),
        ('template_id', 'FK'),
        ('version_number', ''),
        ('file_path', ''),
        ('original_filename', ''),
        ('mime_type', ''),
        ('file_size', ''),
        ('uploaded_by', 'FK'),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'document_template_logs': [
        ('id', 'PK'),
        ('template_id', 'FK'),
        ('version_id', 'FK'),
        ('user_id', 'FK'),
        ('downloaded_at', ''),
        ('ip_address', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'announcements': [
        ('id', 'PK'),
        ('title', ''),
        ('content', ''),
        ('user_id', 'FK'),
        ('created_at', ''),
        ('updated_at', ''),
        ('deleted_at', ''),
    ],
    'sessions': [
        ('id', 'PK'),
        ('user_id', 'FK'),
        ('ip_address', ''),
        ('user_agent', ''),
        ('payload', ''),
        ('last_activity', ''),
    ],
    'password_reset_tokens': [
        ('email', 'PK'),
        ('token', ''),
        ('created_at', ''),
    ],
    'cache': [
        ('key', 'PK'),
        ('value', ''),
        ('expiration', ''),
    ],
    'cache_locks': [
        ('key', 'PK'),
        ('owner', ''),
        ('expiration', ''),
    ],
    'jobs': [
        ('id', 'PK'),
        ('queue', ''),
        ('payload', ''),
        ('attempts', ''),
        ('reserved_at', ''),
        ('available_at', ''),
        ('created_at', ''),
    ],
    'job_batches': [
        ('id', 'PK'),
        ('name', ''),
        ('total_jobs', ''),
        ('pending_jobs', ''),
        ('failed_jobs', ''),
        ('failed_job_ids', ''),
        ('options', ''),
        ('cancelled_at', ''),
        ('created_at', ''),
        ('finished_at', ''),
    ],
    'failed_jobs': [
        ('id', 'PK'),
        ('uuid', 'UK'),
        ('connection', ''),
        ('queue', ''),
        ('payload', ''),
        ('exception', ''),
        ('failed_at', ''),
    ],
    'permissions': [
        ('id', 'PK'),
        ('name', ''),
        ('guard_name', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'roles': [
        ('id', 'PK'),
        ('name', ''),
        ('guard_name', ''),
        ('created_at', ''),
        ('updated_at', ''),
    ],
    'model_has_permissions': [
        ('permission_id', 'FK'),
        ('model_type', ''),
        ('model_id', ''),
    ],
    'model_has_roles': [
        ('role_id', 'FK'),
        ('model_type', ''),
        ('model_id', ''),
    ],
    'role_has_permissions': [
        ('permission_id', 'FK'),
        ('role_id', 'FK'),
    ],
}

# Relationships: (from_entity, to_entity, field_name)
relationships = [
    ('users', 'topics', 'user_id'),
    ('users', 'topics', 'approved_by'),
    ('users', 'topics', 'edited_by'),
    ('users', 'answers', 'user_id'),
    ('users', 'answers', 'verified_by'),
    ('users', 'lessons', 'created_by'),
    ('users', 'document_templates', 'created_by'),
    ('users', 'document_templates', 'updated_by'),
    ('users', 'announcements', 'user_id'),
    ('users', 'topic_attachments', 'uploaded_by'),
    ('users', 'topic_votes', 'user_id'),
    ('users', 'answer_comments', 'user_id'),
    ('users', 'answer_votes', 'user_id'),
    ('users', 'user_lesson_progress', 'user_id'),
    ('users', 'user_quiz_attempts', 'user_id'),
    ('users', 'document_template_versions', 'uploaded_by'),
    ('users', 'document_template_logs', 'user_id'),
    ('users', 'sessions', 'user_id'),
    ('categories', 'topics', 'category_id'),
    ('categories', 'lessons', 'category_id'),
    ('categories', 'category_topic', 'category_id'),
    ('topics', 'category_topic', 'topic_id'),
    ('topics', 'answers', 'topic_id'),
    ('topics', 'topic_attachments', 'topic_id'),
    ('topics', 'topic_votes', 'topic_id'),
    ('answers', 'answer_comments', 'answer_id'),
    ('answers', 'answer_votes', 'answer_id'),
    ('lessons', 'quizzes', 'lesson_id'),
    ('lessons', 'user_lesson_progress', 'lesson_id'),
    ('quizzes', 'quiz_questions', 'quiz_id'),
    ('quizzes', 'user_quiz_attempts', 'quiz_id'),
    ('document_templates', 'document_template_versions', 'template_id'),
    ('document_templates', 'document_template_logs', 'template_id'),
    ('document_template_versions', 'document_template_logs', 'version_id'),
    ('permissions', 'model_has_permissions', 'permission_id'),
    ('permissions', 'role_has_permissions', 'permission_id'),
    ('roles', 'model_has_roles', 'role_id'),
    ('roles', 'role_has_permissions', 'role_id'),
]

def generate_drawio_xml():
    """Generate Draw.io XML file with table format"""
    
    # Layout configuration
    cols = 6
    table_width = 320
    row_height = 30
    header_height = 30
    key_col_width = 80
    name_col_width = 240
    spacing_x = 400
    spacing_y = 400
    start_x = 100
    start_y = 100
    
    # Create root
    root = ET.Element('mxfile')
    root.set('host', 'app.diagrams.net')
    root.set('modified', '2024-01-01T00:00:00.000Z')
    root.set('agent', '5.0')
    root.set('version', '21.0.0')
    root.set('etag', 'test')
    root.set('type', 'device')
    
    diagram = ET.SubElement(root, 'diagram')
    diagram.set('id', 'ERD_Logical')
    diagram.set('name', 'ERD Logical')
    
    mxGraphModel = ET.SubElement(diagram, 'mxGraphModel')
    mxGraphModel.set('dx', '1422')
    mxGraphModel.set('dy', '794')
    mxGraphModel.set('grid', '1')
    mxGraphModel.set('gridSize', '10')
    mxGraphModel.set('guides', '1')
    mxGraphModel.set('tooltips', '1')
    mxGraphModel.set('connect', '1')
    mxGraphModel.set('arrows', '1')
    mxGraphModel.set('fold', '1')
    mxGraphModel.set('page', '1')
    mxGraphModel.set('pageScale', '1')
    mxGraphModel.set('pageWidth', '5000')
    mxGraphModel.set('pageHeight', '5000')
    mxGraphModel.set('math', '0')
    mxGraphModel.set('shadow', '0')
    
    root_cell = ET.SubElement(mxGraphModel, 'root')
    
    # Layer 0 and 1
    mxCell0 = ET.SubElement(root_cell, 'mxCell')
    mxCell0.set('id', '0')
    mxCell1 = ET.SubElement(root_cell, 'mxCell')
    mxCell1.set('id', '1')
    mxCell1.set('parent', '0')
    
    # Generate tables
    table_positions = {}
    entity_list = list(entities_data.keys())
    cell_id_counter = 100
    
    for idx, entity_name in enumerate(entity_list):
        row = idx // cols
        col = idx % cols
        x = start_x + col * spacing_x
        y = start_y + row * spacing_y
        
        # Calculate table height
        num_fields = len(entities_data[entity_name])
        table_height = header_height + (num_fields * row_height)
        
        table_positions[entity_name] = {
            'x': x,
            'y': y,
            'width': table_width,
            'height': table_height,
            'fields': entities_data[entity_name]
        }
        
        # Create table container
        table_id = f'table_{entity_name}'
        cell_id_counter += 1
        
        table_cell = ET.SubElement(root_cell, 'mxCell')
        table_cell.set('id', table_id)
        table_cell.set('value', entity_name)
        table_cell.set('style', 'shape=table;startSize=30;container=1;collapsible=1;childLayout=tableLayout;fixedRows=1;rowLines=0;fontStyle=1;align=center;resizeLast=1;html=1;whiteSpace=wrap;strokeColor=#000000;fillColor=none;')
        table_cell.set('vertex', '1')
        table_cell.set('parent', '1')
        
        table_geom = ET.SubElement(table_cell, 'mxGeometry')
        table_geom.set('x', str(x))
        table_geom.set('y', str(y))
        table_geom.set('width', str(table_width))
        table_geom.set('height', str(table_height))
        table_geom.set('as', 'geometry')
        
        # Header row (Entity name)
        cell_id_counter += 1
        header_row_id = f'{table_id}_header'
        header_row = ET.SubElement(root_cell, 'mxCell')
        header_row.set('id', header_row_id)
        header_row.set('value', '')
        header_row.set('style', 'shape=partialRectangle;collapsible=0;dropTarget=0;pointerEvents=0;fillColor=none;top=0;left=0;bottom=0;right=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;')
        header_row.set('vertex', '1')
        header_row.set('parent', table_id)
        
        header_geom = ET.SubElement(header_row, 'mxGeometry')
        header_geom.set('y', '0')
        header_geom.set('width', str(table_width))
        header_geom.set('height', str(header_height))
        header_geom.set('as', 'geometry')
        
        # Header cell with entity name (spans both columns)
        cell_id_counter += 1
        header_cell_id = f'{header_row_id}_cell'
        header_cell = ET.SubElement(root_cell, 'mxCell')
        header_cell.set('id', header_cell_id)
        header_cell.set('value', entity_name)
        header_cell.set('style', 'shape=partialRectangle;html=1;whiteSpace=wrap;collapsible=0;dropTarget=0;pointerEvents=0;fillColor=none;top=0;left=0;bottom=0;right=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontStyle=1;align=center;')
        header_cell.set('vertex', '1')
        header_cell.set('parent', header_row_id)
        
        header_cell_geom = ET.SubElement(header_cell, 'mxGeometry')
        header_cell_geom.set('y', '0')
        header_cell_geom.set('width', str(table_width))
        header_cell_geom.set('height', str(header_height))
        header_cell_geom.set('as', 'geometry')
        
        # Create rows for each field
        for field_idx, (field_name, key_type) in enumerate(entities_data[entity_name]):
            row_id = f'{table_id}_row_{field_idx}'
            cell_id_counter += 1
            
            # Row container
            row_cell = ET.SubElement(root_cell, 'mxCell')
            row_cell.set('id', row_id)
            row_cell.set('value', '')
            row_cell.set('style', 'shape=partialRectangle;collapsible=0;dropTarget=0;pointerEvents=0;fillColor=none;top=0;left=0;bottom=0;right=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;')
            row_cell.set('vertex', '1')
            row_cell.set('parent', table_id)
            
            row_geom = ET.SubElement(row_cell, 'mxGeometry')
            row_geom.set('y', str(header_height + field_idx * row_height))
            row_geom.set('width', str(table_width))
            row_geom.set('height', str(row_height))
            row_geom.set('as', 'geometry')
            
            # Keys column (left) - like PK in the image
            cell_id_counter += 1
            key_cell_id = f'{row_id}_key'
            key_cell = ET.SubElement(root_cell, 'mxCell')
            key_cell.set('id', key_cell_id)
            key_cell.set('value', key_type if key_type else '')
            key_cell.set('style', 'shape=partialRectangle;html=1;whiteSpace=wrap;collapsible=0;dropTarget=0;pointerEvents=0;fillColor=none;top=0;left=0;bottom=0;right=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;align=left;')
            key_cell.set('vertex', '1')
            key_cell.set('parent', row_id)
            
            key_geom = ET.SubElement(key_cell, 'mxGeometry')
            key_geom.set('y', '0')
            key_geom.set('width', str(key_col_width))
            key_geom.set('height', str(row_height))
            key_geom.set('as', 'geometry')
            
            # Name column (right) - like Row 1, Row 2 in the image
            cell_id_counter += 1
            name_cell_id = f'{row_id}_name'
            name_cell = ET.SubElement(root_cell, 'mxCell')
            name_cell.set('id', name_cell_id)
            name_cell.set('value', field_name)
            name_cell.set('style', 'shape=partialRectangle;html=1;whiteSpace=wrap;collapsible=0;dropTarget=0;pointerEvents=0;fillColor=none;top=0;left=0;bottom=0;right=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;align=left;')
            name_cell.set('vertex', '1')
            name_cell.set('parent', row_id)
            
            name_geom = ET.SubElement(name_cell, 'mxGeometry')
            name_geom.set('x', str(key_col_width))
            name_geom.set('y', '0')
            name_geom.set('width', str(name_col_width))
            name_geom.set('height', str(row_height))
            name_geom.set('as', 'geometry')
    
    # Generate relationships (edges)
    edge_id_counter = 10000
    for from_entity, to_entity, field_name in relationships:
        if from_entity in table_positions and to_entity in table_positions:
            from_pos = table_positions[from_entity]
            to_pos = table_positions[to_entity]
            
            # Find the field row in target table
            target_fields = to_pos['fields']
            field_idx = next((i for i, (name, _) in enumerate(target_fields) if name == field_name), None)
            
            if field_idx is not None:
                # Calculate connection points
                from_center_x = from_pos['x'] + from_pos['width'] / 2
                from_center_y = from_pos['y'] + header_height
                to_center_x = to_pos['x']  # Left side (Keys column)
                to_center_y = to_pos['y'] + header_height + (field_idx + 1) * row_height - row_height / 2
                
                # Create edge
                edge = ET.SubElement(root_cell, 'mxCell')
                edge.set('id', f'edge_{edge_id_counter}')
                edge.set('value', '')
                edge.set('style', 'edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#000000;')
                edge.set('edge', '1')
                edge.set('parent', '1')
                edge.set('source', f'table_{from_entity}')
                edge.set('target', f'table_{to_entity}')
                
                edge_geom = ET.SubElement(edge, 'mxGeometry')
                edge_geom.set('relative', '1')
                edge_geom.set('as', 'geometry')
                
                # Add waypoints for clean routing
                mxPoint1 = ET.SubElement(edge_geom, 'mxPoint')
                mxPoint1.set('x', str(from_center_x))
                mxPoint1.set('y', str(from_center_y))
                mxPoint1.set('as', 'sourcePoint')
                
                mxPoint2 = ET.SubElement(edge_geom, 'mxPoint')
                mxPoint2.set('x', str(to_center_x))
                mxPoint2.set('y', str(to_center_y))
                mxPoint2.set('as', 'targetPoint')
                
                edge_id_counter += 1
    
    # Convert to string and format
    xml_str = ET.tostring(root, encoding='unicode')
    dom = minidom.parseString(xml_str)
    pretty_xml = dom.toprettyxml(indent='  ')
    
    return pretty_xml

if __name__ == '__main__':
    xml_content = generate_drawio_xml()
    output_file = 'deskripsi_erd_logis_lengkap.drawio'
    
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(xml_content)
    
    print(f"Draw.io file generated: {output_file}")
