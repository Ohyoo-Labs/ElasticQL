/* 
* ElasticQL NoSQLDataManager.js module class
 */
import BaseDataManager from '../core/BaseDataManager.js';
export default class NoSQLDataManager extends BaseDataManager {
  _buildQueryString(params) {
    const { schema, fields, rels, filters } = params;
    const queryParams = new URLSearchParams();
    
    queryParams.append('schema', schema);
    queryParams.append('fields', fields.join(','));
    if (rels) queryParams.append('rels', JSON.stringify(rels));
    if (filters) queryParams.append('filters', JSON.stringify(filters));

    return queryParams.toString();
  }
}