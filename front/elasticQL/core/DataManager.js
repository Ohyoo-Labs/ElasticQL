/*
 * ElasticQL DataManager JS module class
 */
export class DataManager {
  constructor(baseUrl, mode = "nosql") {
    this.baseUrl = baseUrl;
    this.mode = mode;
    this.managerPromise = this.initializeManager();
  }

  async initializeManager() {
    if (this.mode === "sql") {
      /* const SQLDataManager = (await import("./services/SQLDataManager.js").default);
      return new SQLDataManager(this.baseUrl); */
      return await import("../services/SQLDataManager.js").then(({ default: SQLDataManager }) => {
        return new SQLDataManager(this.baseUrl);
      });
    } else {
      /* const NoSQLDataManager = await import("./services/NoSQLDataManager.js").default;
      return new NoSQLDataManager(this.baseUrl); */
      return await import("../services/NoSQLDataManager.js").then(({ default: NoSQLDataManager }) => {
        return new NoSQLDataManager(this.baseUrl);
      });
    }
  }

  async getManager() {
    return await this.managerPromise;
  }

  async query(params) {
    const manager = await this.getManager();
    return manager.query(params);
  }

  async create(params) {
    const manager = await this.getManager();
    return manager.create(params);
  }

  async update(params) {
    const manager = await this.getManager();
    return manager.update(params);
  }

  async delete(params) {
    const manager = await this.getManager();
    return manager.delete(params);
  }
}
